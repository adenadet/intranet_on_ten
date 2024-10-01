<?php

namespace App\Http\Controllers;

use App\Models\Lms\Lesson;
use App\Models\Policy\Policy;
use Illuminate\Http\Request;

class ModulesController extends Controller
{
    public function chats()
    {
        $params = [
            'page_title' => 'Chats',
        ];
        return view('chats')->with($params);
    }

    public function contacts()
    {
        $params = [
            'page_title' => 'Contacts',
        ];
        return view('app')->with($params);
    }

    public function dashboard()
    {
        $params = [
            'page_title' => 'Dashboard',
        ];
        return view('app')->with($params);
    }

    public function departments()
    {
        $params = [
            'page_title' => 'Departments',
        ];
        return view('app')->with($params);
    }

    public function hrms()
    {
        $params = [
            'page_title' => 'Human Resources Management',
        ];
        return view('app')->with($params);
    }

    public function hrms_admin()
    {
        $params = [
            'page_title' => 'Human Resources Administrator',
        ];
        return view('app')->with($params);
    }

    public function internet()
    {
        $params = [
            'page_title' => 'Internet',
        ];
        return view('app')->with($params);
    }


    public function inventory()
    {
        $params = [
            'page_title' => 'Inventory',
        ];
        return view('app')->with($params);
    }

    public function notices()
    {
        $params = ['page_title' => 'Notice Board',];
        return view('app')->with($params);
    }

    public function policies()
    {
        $params = [
            'page_title' => 'Policies',
        ];
        return view('app')->with($params);
    }

    public function policy_reader($id)
    {

        $params = [
            'page_title' => 'Policies',
            'policy' => Policy::where('id', '=', $id)->first(),
        ];
        return view('policies.reader')->with($params);
    }

    public function profile()
    {
        $params = [
            'page_title' => 'Profile',
        ];
        return view('app')->with($params);
    }

    public function settings()
    {
        $params = [
            'page_title' => 'Settings',
        ];
        return view('app')->with($params);
    }

    public function staff_month()
    {
        $params = [
            'page_title' => 'Staff of the Month',
        ];
        return view('app')->with($params);
    }

    public function ticketing()
    {
        $params = [
            'page_title' => 'Tickets',
        ];
        return view('app')->with($params);
    }

    public function users()
    {
        $params = [
            'page_title' => 'Users',
        ];
        return view('app')->with($params);
    }
}
