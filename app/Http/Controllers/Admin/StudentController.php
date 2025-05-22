<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB, Auth, File, Storage};
use Intervention\Image\Facades\Image;

class StudentController extends Controller
{
    public function index() {
        $students = DB::table("students")->get();
        return view('admin.student.index', compact('students'));
    }
    public function show() {

    }
    public function create() {
        return view('admin.student.create');
    }
    public function store(Request $request) {
        $validated = $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required',
            'dob' => 'required',
            'gender' => 'required',
            'skills' => 'required',
            'contact' => 'required',
            'address' => 'required',

        ]);

        $skills =  $request->skills;
        $stringSkills =  implode(", ", $skills);
        $data = [
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'dob' => $request->dob,
            'gender' => $request->gender,
            'skills' => $stringSkills,
            'contact' => $request->contact,
            'address' => $request->address,
            'created_at' => now(),

        ];
        $photo = $request->photo;
        if ($photo) {
            $photoName = $request->first_name ."_".$request->last_name."-". uniqid() . '.' . $photo->getClientOriginalExtension();

            $image = Image::make($photo)->resize(300, 300);

            $path = storage_path('app/public/uploads/students/');

            $image->save($path . $photoName);
            $data["photo"] = "storage/uploads/students/" . $photoName;

        } else {
            $data["photo"] = "storage/uploads/students/empty-user.webp";
        }
        $insert = DB::table("students")->insert($data);
        if($insert) {
            return redirect()->back()->with('success', 'Student Created Successfully!');
        } else {
            return redirect()->back()->with('error', 'Something went wrong!');

        }

    }
    public function edit($id) {
        $student = DB::table("students")->where("id", $id)->first();
        $studentSkills = explode(", ", $student->skills);
        $allSkills = ['HTML', 'CSS', 'JavaScript', 'PHP', 'MySQL', 'Node.js', 'Git & Github'];


        return view("admin.student.edit", compact("student", "studentSkills", "allSkills"));
    }
    public function update(Request $request, $id) {
        $validated = $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required',
            'dob' => 'required',
            'gender' => 'required',
            'skills' => 'required',
            'contact' => 'required',
            'address' => 'required',

        ]);
        $skills =  $request->skills;
        $stringSkills =  implode(", ", $skills);
        $data = [
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'dob' => $request->dob,
            'gender' => $request->gender,
            'skills' => $stringSkills,
            'contact' => $request->contact,
            'address' => $request->address,
            'updated_at' => now(),

        ];
        $photo = $request->photo;
        $img= DB::table("students")->where("id", $id)->first()->photo;

        if ($photo) {

            // remove current photo
            $oldImagePath = public_path($img); // full path
            if (File::exists($oldImagePath)) {
                if (File::basename($img) != "empty-user.webp") {
                    File::delete($oldImagePath);
                }
            }


            $photoName = $request->first_name ."_".$request->last_name."-". uniqid() . '.' . $photo->getClientOriginalExtension();

            $image = Image::make($photo)->resize(300, 300);

            $path = storage_path('app/public/uploads/students/');

            $image->save($path . $photoName);
            $data["photo"] = "storage/uploads/students/" . $photoName;

        }
        $update = DB::table("students")->where("id", $id)->update($data);
        if($update) {
            return redirect()->route("student.index")->with('success', 'Student Updated Successfully!');
        } else {
            return redirect()->back()->with('error', 'Something went wrong!');

        }
    }


    public function destroy($id) {
        $img= DB::table("students")->where("id", $id)->first()->photo;
        // remove  photo
        $oldImagePath = public_path($img); // full path
        if (File::exists($oldImagePath)) {
            if (File::basename($img) != "empty-user.webp") {
                File::delete($oldImagePath);
            }
        }
        $delete = DB::table("students")->where("id", $id)->delete();
        if($delete) {
            return redirect()->back()->with('success', 'Student Deleted Successfully!');
        } else {
            return redirect()->back()->with('error', 'Something went wrong!');

        }
    }
}
