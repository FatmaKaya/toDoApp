<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Http\Controllers\Core;
use App\Models\Task;
use App\Models\Developer;

class HomeController extends Controller
{
    public function index(){
        Task::addTasks();
        $developers = Developer::all();
        $datas["developerTasks"] = Developer::getPlan($developers->toArray());    
        return view("home",$datas);
    }
}
