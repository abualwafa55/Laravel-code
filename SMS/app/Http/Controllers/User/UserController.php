<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\UserRepository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * @var UserRepository
     */
    protected $users;

    /**
     * UserController constructor.
     *
     * @param  UserRepository  $users
     */
    public function __construct(UserRepository $users)
    {
        $this->users = $users;
    }

    /**
     * Show user homepage.
     *
     * @return Application|Factory|\Illuminate\Contracts\View\View|View
     */
    public function index()
    {

        $breadcrumbs = [
                ['link' => url('dashboard'), 'name' => __('locale.menu.Dashboard')],
                ['name' => Auth::user()->displayName()],
        ];


        return view('customer.dashboard', compact('breadcrumbs'));
    }

}
