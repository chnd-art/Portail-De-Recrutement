<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\Admin;
use App\Models\Job\Job;
use App\Models\Category\Category;
use App\Models\Job\Application;
use Illuminate\Support\Facades\Hash;
use File;


class AdminsController extends Controller
{
    public function viewLogin() {

        return view("admins.view-login");
     }


     public function checkLogin(Request $request) {
        $remember_me = $request->has('remember_me') ? true : false;

        if (auth()->guard('admin')->attempt(['email' => $request->input("email"), 'password' => $request->input("password")], $remember_me)) {
            \Log::info('Authentication successful');

            
            return redirect() -> route('admins.dashboard');
        }
        \Log::info('Authentication failed');

        return redirect()->back()->with(['error' => 'error logging in']);

     }

     public function index() {

        $jobs = Job::select()->count();
        $categories = Category::select()->count();
        $admins = Admin::select()->count();
        $applications = Application::select()->count();



        return view("admins.index", compact('jobs','categories','admins','applications'));
     }

     public function admins() {

      $admins = Admin::all();
      return view("admins.all-admins",compact('admins'));
   }
   

   public function createAdmins() {

      return view("admins.create-admins");
   }

   public function storeAdmins(Request $request) {

      Request()->validate([
         "name" => "required|max:40",
         "email" => "required|max:40",
         "password" => "required",
      ]);

      $creatAdmins = Admin::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
      ]);

      if($creatAdmins) {
         return redirect('/admin/all-admins/')->with('create', 'Admin updated succefully');
      }
   }
   
   public function displayCategories() {

      $categories = category::all();
      return view("admins.display-categories",compact('categories'));
   }
   
   public function createCategories() {

      return view("admins.create-categories");
   }

   public function storeCategories(Request $request) {

      Request()->validate([
         "name" => "required|max:40",
         
      ]);

      $creatCategories = Category::create([
        'name' => $request->name,
       
      ]);

      if($creatCategories) {
         return redirect('/admin/display-categories/')->with('create', 'Category created succefully');
      }
   }

   public function editCategories  ($id) {
       
        $category = category::find($id);

      
         return view('admins.update-categories',compact('category'));
      }

      

      public function UpdateCategories (Request $request , $id) {

         $validatedData = Request()->validate([
            "name" => "required|max:40",
            
         ]);
   
         $updateCategory = Category::find($id);
         $updateCategory->update($validatedData);
          

         // update() cannot be called statically
         // $updateCategory = Category::update([
         //   "name" => $request->name,
         // ]);
   
         if($updateCategory) {
            return redirect('/admin/display-categories/')->with('update', 'Category updated succefully');
         }
      }

      public function deleteCategories ($id) {

         $category = category::find($id);
         $category->delete();


         if($category) {
            return redirect('/admin/display-categories/')->with('delete', 'Category deleted succefully');
         }
      }

      public function displayJobs() {

         $jobs = Job::all();
         return view("admins.display-jobs",compact('jobs'));
      }

      public function createJobs() {
         $categories = category::all();

         return view("admins.create-jobs",compact('categories'));
      }

      public function storeJobs(Request $request) {

           Request()->validate([
             "job_title" => "required|max:40",
             "job_region" => "required|max:40",
             "company" => "required",
             "job_type" => "required",
             "vacancy" => "required",
             "experience" => "required",
             "salary" => "required",
             "gender" => "required",
             "application_deadline" => "required",
             "job_description" => "required",
             "responsibilities" => "required",
             "education_experience" => "required",
             "other_benifits" => "required",
             "category" => "required",
             'image' => "required",
          ]);
   

            // Set the destination path for the uploaded image
            $destinationPath = 'assets/images/';

            // Get the original name of the uploaded image
            $myimage = $request->image->getClientOriginalName();

            // Move the uploaded image to the destination path
            $request->image->move(public_path($destinationPath), $myimage);


         $creatJobs = Job::create([
            'job_title' => $request->job_title,
            'job_region' => $request->job_region,
            'company' => $request->company,
            'job_type' => $request->job_type,
            'vacancy' => $request->vacancy,
            'experience' =>$request->experience,
            'salary' => $request->salary,
            'gender' => $request->gender,
            'application_deadline' => $request->application_deadline,
            'job_description'=> $request->job_description,
            'responsibilities' => $request->responsibilities,
            'education_experience' => $request->education_experience,
            'other_benifits' => $request->other_benifits,
            'category' => $request->category,
             'image' => $myimage, 
           
         ]);
   
         if($creatJobs) {
            return redirect('/admin/display-jobs/')->with('create', 'Job updated succefully');
         }
      }

      public function deleteJobs ($id) {

         $deleteJob = Job::find($id);

         if(File::exists(public_path('assets/images/'.$deleteJob->image))){
            File::delete(public_path('assets/images/'.$deleteJob->image));
         }else{
           //dd('File does not exits.');
         }
         $deleteJob->delete();


         if($deleteJob) {
            return redirect('/admin/display-jobs/')->with('delete', 'Job  deleted succefully');
         }
      }

      public function displayApplication() {

         $applications = Application::all();
         return view("admins.display-applications",compact('applications'));
      }

      public function deleteApplication ($id) {

         $application = Application::find($id);
         $application->delete();


         if($application) {
            return redirect('/admin/display-application/')->with('delete', 'Application  deleted succefully');
         }
      }
      

    

   

   
}
