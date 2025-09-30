<?php

namespace Modules\Team\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;
use Modules\Language\App\Models\Language;
use Modules\Team\App\Models\Team;
use Modules\Team\App\Models\TeamTranslation;

class TeamController extends Controller
{

    public function index()
    {
        $teams = Team::latest()->get();

        return view('team::index', compact('teams'));
    }


    public function create()
    {
        return view('team::create');
    }


    public function store(Request $request)
    {
        $request->validate(
            [
                'name' => 'required|string',
                'slug' => 'required|string|unique:teams,slug',
                'image' => 'required|image',
                'image_detail' => 'nullable|image',
                'description' => 'required|string',
                'designation' => 'required|string',
                'mail' => 'required|email',
                'phone_number' => 'required|string',
                'facebook' => 'nullable|string|url',
                'twitter' => 'nullable|string|url',
                'linkedin' => 'nullable|string|url',
                'instagram' => 'nullable|string|url',
                'skill_title.*' => 'nullable|string|max:255',
                'skill_percentage.*' => 'nullable|numeric|min:0|max:100',
            ],
            [
                'name.required' => trans('translate.Name is required'),
                'slug.required' => trans('translate.Slug is required'),
                'description.required' => trans('translate.Description is required'),
                'designation.required' => trans('translate.Designation is required'),
                'mail.required' => trans('translate.Mail is required'),
                'phone_number.required' => trans('translate.Phone Number is required'),
                'skill_title.*.required' => trans('translate.Skill Title is required'),
                'skill_percentage.*.required' => trans('translate.Skill Percentage is required'),
                'skill_percentage.*.min' => trans('translate.Skill Percentage must be less than or equal to 100'),
                'skill_percentage.*.max' => trans('translate.Skill Percentage must be less than or equal to 100'),
            ]
        );

        $team = new Team();

        if ($request->image) {
            $image_name = 'team' . date('-Y-m-d-h-i-s-') . rand(999, 9999) . '.webp';
            $image_name = 'uploads/custom-images/' . $image_name;
            Image::make($request->image)
                ->encode('webp', 80)
                ->save(public_path() . '/' . $image_name);
            $team->image = $image_name;
        }

        if ($request->image_detail) {
            $image_name = 'team' . date('-Y-m-d-h-i-s-') . rand(999, 9999) . '.webp';
            $image_name = 'uploads/custom-images/' . $image_name;
            Image::make($request->image_detail)
                ->encode('webp', 80)
                ->save(public_path() . '/' . $image_name);
            $team->image_details = $image_name;
        }

        $team->slug = $request->slug;
        $team->mail = $request->mail;
        $team->phone_number = $request->phone_number;
        $team->facebook = $request->facebook;
        $team->twitter = $request->twitter;
        $team->linkedin = $request->linkedin;
        $team->instagram = $request->instagram;
        $team->pinterest = $request->pinterest;
        $team->website = $request->website;
        $team->address = $request->address;

        $team->save();

        $languages = Language::all();
        foreach ($languages as $language) {
            $team_translate = TeamTranslation::firstOrNew([
                'lang_code' => $language->lang_code,
                'team_id' => $team->id,
            ]);

            $skills = [];
            foreach ($request->skill_title as $index => $title) {
                $skills[] = [
                    'title' => $title,
                    'percentage' => (int)$request->skill_percentage[$index],
                ];
            }

            $team_translate->skill_short_description = $request->skill_short_description;
            $team_translate->information = $request->information;
            $team_translate->skill_list = $skills;
            $team_translate->name = $request->name;
            $team_translate->description = $request->description;
            $team_translate->designation = $request->designation;
            $team_translate->save();
        }

        $notify_message = trans('translate.Created Successfully');
        $notify_message = array('message' => $notify_message, 'alert-type' => 'success');

        return redirect()->route('admin.team.index')->with($notify_message);
    }



    public function edit(Request $request, $id)
    {
        $teamMember = Team::findOrFail($id);
        $team_translate = TeamTranslation::where(['team_id' => $id, 'lang_code' => $request->lang_code])->first();
        return view('team::edit', compact('teamMember', 'team_translate'));
    }


    public function update(Request $request, $id): RedirectResponse
    {
        $team = Team::findOrFail($id);

        if ($request->lang_code == admin_lang()) {

            $request->validate(
                [
                    'image' => 'nullable|image',
                    'name' => 'required|string',
                    'slug' => 'required|string|unique:teams,slug,' . $id,
                    'description' => 'required|string',
                    'designation' => 'required|string',
                    'mail' => 'required|email',
                    'phone_number' => 'required|string',
                    'facebook' => 'nullable|string|url',
                    'twitter' => 'nullable|string|url',
                    'linkedin' => 'nullable|string|url',
                    'instagram' => 'nullable|string|url',
                    'skill_title.*' => 'nullable|string|max:255',
                    'skill_percentage.*' => 'nullable|numeric|min:0|max:100',
                ],
                [
                    'name.required' => trans('translate.Name is required'),
                    'slug.required' => trans('translate.Slug is required'),
                    'description.required' => trans('translate.Description is required'),
                    'designation.required' => trans('translate.Designation is required'),
                    'mail.required' => trans('translate.Mail is required'),
                    'phone_number.required' => trans('translate.Phone Number is required'),
                    'skill_title.*.required' => trans('translate.Skill Title is required'),
                    'skill_percentage.*.required' => trans('translate.Skill Percentage is required'),
                    'skill_percentage.*.min' => trans('translate.Skill Percentage must be less than or equal to 100'),
                    'skill_percentage.*.max' => trans('translate.Skill Percentage must be less than or equal to 100'),
                ]
            );

            if ($request->image) {
                $old_image = $team->image;
                $image_name = 'team' . date('-Y-m-d-h-i-s-') . rand(999, 9999) . '.webp';
                $image_name = 'uploads/custom-images/' . $image_name;
                Image::make($request->image)
                    ->encode('webp', 80)
                    ->save(public_path() . '/' . $image_name);
                $team->image = $image_name;
                $team->save();

                if ($old_image) {
                    if (File::exists(public_path() . '/' . $old_image)) unlink(public_path() . '/' . $old_image);
                }
            }

            if ($request->image_detail) {
                $old_image = $team->image_details;
                $image_name = 'team' . date('-Y-m-d-h-i-s-') . rand(999, 9999) . '.webp';
                $image_name = 'uploads/custom-images/' . $image_name;
                Image::make($request->image_detail)
                    ->encode('webp', 80)
                    ->save(public_path() . '/' . $image_name);
                $team->image_details = $image_name;
                $team->save();

                if ($old_image) {
                    if (File::exists(public_path() . '/' . $old_image)) unlink(public_path() . '/' . $old_image);
                }
            }

            $team->slug = $request->slug;
            $team->mail = $request->mail;
            $team->phone_number = $request->phone_number;
            $team->facebook = $request->facebook;
            $team->twitter = $request->twitter;
            $team->linkedin = $request->linkedin;
            $team->instagram = $request->instagram;
            $team->pinterest = $request->pinterest;
            $team->website = $request->website;
            $team->address = $request->address;
            $team->save();
        }


        $team_translate = TeamTranslation::where(['id' => $request->team_id])->first();
        $team_translate->name = $request->name;
        $team_translate->designation = $request->designation;
        $team_translate->description = $request->description;

        $skills = [];
        foreach ($request->skill_title as $index => $title) {
            $skills[] = [
                'title' => $title,
                'percentage' => (int)$request->skill_percentage[$index],
            ];
        }

        $team_translate->skill_short_description = $request->skill_short_description;
        $team_translate->information = $request->information;
        $team_translate->skill_list = $skills;

        $team_translate->save();


        $notify_message = trans('translate.Updated Successfully');
        $notify_message = array('message' => $notify_message, 'alert-type' => 'success');
        return redirect()->back()->with($notify_message);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $team = Team::findOrFail($id);
        $old_image = $team->image;
        $old_image2 = $team->image_details;

        if ($old_image) {
            if (File::exists(public_path() . '/' . $old_image)) unlink(public_path() . '/' . $old_image);
        }

        if ($old_image2) {
            if (File::exists(public_path() . '/' . $old_image2)) unlink(public_path() . '/' . $old_image2);
        }

        TeamTranslation::where('team_id', $id)->delete();
        $team->delete();

        $notify_message =  trans('translate.Delete Successfully');
        $notify_message = array('message' => $notify_message, 'alert-type' => 'success');
        return redirect()->route('admin.team.index')->with($notify_message);
    }

    public function setup_language($lang_code)
    {
        $blog_translates = TeamTranslation::where('lang_code', admin_lang())->get();

        foreach ($blog_translates as $team_translate) {
            $new_trans = new TeamTranslation();
            $new_trans->lang_code = $lang_code;
            $new_trans->team_id = $team_translate->team_id;
            $new_trans->name = $team_translate->name;
            $new_trans->description = $team_translate->description;
            $new_trans->designation = $team_translate->designation;
            $new_trans->save();
        }
    }
}
