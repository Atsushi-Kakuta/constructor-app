<?php

namespace App\Http\Controllers;

use App\Models\Constructor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;

class ConstructorController extends Controller
{
    public function index()
    {
        $offices = config("env");
        if ($search_word = request("search")) {
            $constructs = Constructor::where("hashtag", "LIKE", "%" . $search_word . "%")->orwhere("office","LIKE", "%" . $search_word. "%")->get();
        } else {
            $constructs = Constructor::get();
        }
        return view("construct.index", compact("constructs", "offices"));
    }

    public function create()
    {
        $offices = config("env");
        return view("construct.create", compact("offices"));
    }

    public function store(Request $request)
    {
        $form_items = $request->all();
        $office_name = $this->processing_office_name($form_items);
        $route_name = $this->processing_route_name($form_items);
        Constructor::create(["location" => $form_items["location"], "hashtag" => $form_items["hashtag"],"editor" => $form_items["editor"],"business_name" => $form_items["business_name"],"route" => $route_name,"real_work_time" => $form_items["real_work"],"bus_station" =>$form_items["bus_station"],"bus_relocation_flag" => $form_items["relocation_bus"] ?? 0,"remarks" => $form_items["remarks"] ,"office" => $office_name, "detail" => $form_items["detail"], "started_at" => $form_items["start"], "ended_at" => $form_items["end"]]);
        return redirect("/construct");
    }

    public function edit($id, Request $request)
    {
        $construct_data = Constructor::find($id);
        $edit_mode = $request->query("edit_mode");
        return view("construct.edit", compact("construct_data", "edit_mode"));
    }

    public function update(Request $request)
    {
        $form_items = $request->all();
        $construct_id = $form_items["construct_id"];
        Constructor::where("id", $construct_id)->update(["location" => $form_items["location"], "office" => $form_items["office"], "detail" => $form_items["detail"], "started_at" => $form_items["started_at"], "ended_at" => $form_items["ended_at"]]);
        return redirect("/construct/edit/" . $construct_id);
    }

    public function delete(Request $request)
    {
        $construct_id = $request->input("construct_id");
        Constructor::where("id", $construct_id)->delete();
        return redirect("/construct");
    }

    private function processing_office_name($form_items)
    {
        $office_name = "";
        foreach ($form_items as $form_item) {
            if (strpos($form_item, "?????????")) {
                $office_name .= $form_item . ",";
            }
        }
        return substr($office_name, 0, -1);
    }
    private function processing_route_name($form_items)
    {
        $route_name = "";
        foreach ($form_items as $key => $data) {
            if (strpos($key, "item") !== false) {
                $route_name .= $data . ",";
            }
        }
        return substr($route_name, 0, -1);
    }

}
