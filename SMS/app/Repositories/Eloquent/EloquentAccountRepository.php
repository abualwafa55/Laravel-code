<?php

namespace App\Repositories\Eloquent;

use App\Models\Language;
use App\Models\Notifications;
use App\Models\User;
use App\Models\SocialLogin;
use App\Notifications\TwoFactorCode;
use App\Repositories\Contracts\UserRepository;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\MassAssignmentException;
use Illuminate\Support\Arr;
use App\Exceptions\GeneralException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Contracts\Auth\Authenticatable;
use App\Repositories\Contracts\AccountRepository;
use Illuminate\Support\Facades\Session;
use Throwable;


/**
 * Class EloquentAccountRepository.
 */
class EloquentAccountRepository extends EloquentBaseRepository implements AccountRepository
{
    /**
     * @var UserRepository
     */
    protected $users;

    /**
     * EloquentUserRepository constructor.
     *
     * @param  User  $user
     * @param  UserRepository  $users
     *
     * @internal param \Illuminate\Contracts\Config\Repository $config
     */
    public function __construct(User $user, UserRepository $users)
    {
        parent::__construct($user);
        $this->users = $users;
    }

    /**
     * @param  array  $input
     *
     * @return User
     * @throws Exception
     *
     * @throws Throwable
     */
    public function register(array $input): User
    {
        // Registration is not enabled
        if ( ! config('account.can_register')) {
            throw new GeneralException(__('locale.exceptions.registration_disabled'));
        }

        $user = $this->users->store([
                'first_name'  => $input['first_name'],
                'last_name'   => $input['last_name'],
                'email'       => $input['email'],
                'password'    => $input['password'],
                'status'      => true,
                'phone'       => null,
                'is_customer' => true,
        ], true);

        //
        if (config('account.verify_account')) {
            $user->sendEmailVerificationNotification();
        }

        Notifications::create([
                'user_id' => 1,
                'notification_for' => 'admin',
                'notification_type' => 'user',
                'message' => $user->displayName() .' Registered',
        ]);

        return $user;
    }

    /**
     *
     * get user data
     *
     * @param $provider
     * @param $data
     *
     * @return User|mixed
     * @throws GeneralException
     */
    public function findOrCreateSocial($provider, $data): User
    {
        // Email can be not provided, so set default provider email.
        $user_email = $data->getEmail() ?: "{$data->getId()}@{$provider}.com";

        // Get user with this email or create new one.
        /** @var User $user */
        $user = $this->users->query()->whereEmail($user_email)->first();

        if ( ! $user) {
            // Registration is not enabled
            if ( ! config('account.can_register')) {
                throw new GeneralException(__('locale.exceptions.registration_disabled'));
            }

            $last_name = null;

            if ($data->getName()) {
                $first_name = $data->getName();
                $last_name  = $data->getNickname();
            } else {
                $first_name = $data->getNickname();
            }

            $user = $this->users->store([
                    'first_name'  => $first_name,
                    'last_name'   => $last_name,
                    'email'       => $user_email,
                    'status'      => true,
                    'phone'       => null,
                    'is_customer' => true,
            ], true);

        }
        if ($user) {
            $user->provider    = $provider;
            $user->provider_id = $data->getId();
            $user->image       = $data->getAvatar();
            $user->save();
        }

        return $user;


    }

    /**
     * @param  Authenticatable  $user
     * @param $name
     *
     * @return bool
     */
    public function hasPermission(Authenticatable $user, $name): bool
    {

        /** @var User $user */
        // First user is always super admin and cannot be deleted
        if ($user->id === 1) {
            return true;
        }

        $permissions = Session::get('permissions');

        if ($permissions == null && $user->is_customer) {
            $permissions = collect(json_decode($user->customer->permissions, true));
        }

        if ($permissions->isEmpty()) {
            return false;
        }

        return $permissions->contains($name);
    }

    /**
     * @param $input
     *
     * @return mixed
     *
     * @throws MassAssignmentException
     */
    public function update(array $input)
    {

        $availLocale = Session::get('availableLocale');

        if ( ! isset($availLocale)) {
            $availLocale = Language::where('status', 1)->select('code')->cursor()->map(function ($name) {
                return $name->code;
            })->toArray();

            session()->put('availableLocale', $availLocale);
        }

        // check for existing language
        if (in_array($input['locale'], $availLocale)) {
            session()->put('locale', $input['locale']);
        }

        /** @var User $user */
        $user = auth()->user();
        $user->fill(Arr::only($input, ['first_name', 'last_name', 'email', 'locale', 'timezone', 'password']));
        $user->save();

        return response()->json([
                'status'  => 'success',
                'message' => __('locale.customer.profile_was_successfully_updated'),
        ]);
    }

    /**
     * @return mixed
     * @throws GeneralException|Exception
     *
     */
    public function delete(): bool
    {
        /** @var User $user */
        $user = auth()->user();

        if ($user->is_super_admin) {
            throw new GeneralException(__('exceptions.backend.users.first_user_cannot_be_destroyed'));
        }

        if ( ! $user->delete()) {
            throw new GeneralException(__('exceptions.frontend.user.delete_account'));
        }

        return true;
    }

    /**
     * @param  Authenticatable  $user
     *
     * @return Authenticatable
     * @throws GeneralException
     */

    public function redirectAfterLogin(Authenticatable $user): Authenticatable
    {
        if (config('app.two_factor') === false || $user->two_factor == 0 || Session::get('two-factor-login-success') == 'success' || config('app.env') == 'demo') {
            $user->last_access_at = Carbon::now();
            if ($user->is_admin === true) {
                $user->active_portal = 'admin';
                session(['permissions' => $user->getPermissions()]);
            } else {
                $user->active_portal = 'customer';
                $permissions         = collect(json_decode($user->customer->permissions, true));
                session(['permissions' => $permissions]);
            }

            if ( ! $user->save()) {
                throw new GeneralException('Something went wrong. Please try again.');
            }

            return $user;
        }

        if (config('app.two_factor') && $user->two_factor && config('app.env') != 'demo') {
            $user->generateTwoFactorCode();
            $user->notify(new TwoFactorCode());
        }

        return $user;
    }

}
