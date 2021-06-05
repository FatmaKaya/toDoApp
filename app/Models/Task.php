<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ixudra\Curl\Facades\Curl;
use Illuminate\Support\facades\http;
use App\Models\Provider;
use App\Models\Task;

class Task extends Model
{
    protected $table = "task";
    protected $fillable = [
        'provider',
        'name',
        'level',
        'duration'
    ];
    
    public static function addTasks()
    {
        $providers = Provider::get();
        foreach ($providers as $provider) {
            $parameters = explode(",",$provider->parameters);
            $tasks = Task::listTasks($provider->endpoint,$parameters);
            foreach ($tasks as $task) {
                $taskCount = Task::where(["provider"=>$provider->id,"name"=>$task->name])->count();
                if($taskCount==0){
                    $taskObject = new Task();
                    $taskObject->provider           = $provider->id;
                    $taskObject->name               = $task->name;
                    $taskObject->level              = $task->level;
                    $taskObject->duration = $task->duration;
                    $taskObject->save();
                }
            }
        }
    }

    public static function listTasks($endpoint,$parameters)
    {
        $client = new \GuzzleHttp\Client();
        $res = $client->request("GET", $endpoint);
        $responseBody = json_decode($res->getBody());

        foreach ($responseBody as $key=>$item) {
            if($parameters[2]=="{key}"){
                foreach ($item as $dataName=>$property) {
                    $name               = $dataName;
                    $level              = $property->{$parameters[0]};
                    $duration = $property->{$parameters[1]};
                }
            }
            else{
                foreach ($parameters as $parameter) {
                    $param[] = explode(".",$parameter);
                }
                
                if(count($param[0])>1)
                    $level = $item->{$param[0][0]}->{$param[0][1]};
                else
                    $level = $item->{$param[0][0]};

                if(count($param[1])>1)
                    $duration = $item->{$param[1][0]}->{$param[1][1]};
                else
                    $duration = $item->{$param[1][0]};

                if(count($param[2])>1)
                    $name = $item->{$param[2][0]}->{$param[2][1]};
                else
                    $name = $item->{$param[2][0]};

            }

            $task = new \stdClass();
            $task->name                 = $name;
            $task->level                = $level;
            $task->duration   = $duration;
            $newData[] = $task;

          
        }
        return $newData;
    }

}
