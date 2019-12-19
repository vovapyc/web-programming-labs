<?php

namespace App\Http\Controllers;

use App\Company;
use App\User;
use App\Vacancy;
use Cviebrock\EloquentSluggable\Services\SlugService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UploadParsedVacanciesController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        // Check user ip AND token
        if ($request->ip() === env('ALLOWED_IP_FOR_UPLOAD_VACANCIES')) {
            $file = $request->file('file');

            if ($file->isValid()) {
                // Check file hash
                $fileHash = hash_file('sha256', $file->path());
                // if (hash('sha256', env('UPLOAD_VACANCIES_TOKEN') . ':' . $fileHash) === $request->header('Token')) {
                if (env('UPLOAD_VACANCIES_TOKEN') === $request->header('Token')) {
                    // Unzip file to temporary folder
                    $Path = $file->path();
                    \Zipper::make($Path)->extractTo('/tmp/');

                    // Get json file and unmarshal it
                    $strJsonFileContents = file_get_contents("/tmp/vacancies.json");
                    $vacancies = json_decode($strJsonFileContents, true);

                    foreach ($vacancies as $parsed_vacancy) {
                        $this->createVacancy($parsed_vacancy);
                    }

                    return response('', 200);
                }
            }
        }
        return response('', 403);
    }

    /**
     * Check if company exists and add it if not, takes vacancy object and name of company
     *
     * @param string $comp_name
     * @return int|void
     */
    public function createCompany($comp_name)
    {
        $cmp = Company::where('name', '=', $comp_name)->first();
        if ($cmp != null) {
            return $cmp->id;
        }

        $newUser = $this->createUser($comp_name);

        if (!$newUser) {
            return;
        }

        $company = new Company();
        $company->name = $comp_name;
        $company->slug = $newUser->slug;
        $company->user_id = $newUser->id;
        $company->status = "Компанія додана через парсинг";
        if ($company->save()) {
            return $company->id;
        }
        return null;
    }

    //create User
    public function createUser($comp_name)
    {
        $pwdSl = hash('crc32', time());
        $cmpSL = hash('crc32', $comp_name);
        $pwd = $cmpSL . $pwdSl;

        $slug = hash('crc32', $comp_name . time());

        $email = $slug . "@robby.work";
        $newUser = User::create([
            'name' => $comp_name,
            'email' => $email,
            'password' => bcrypt($pwd),
            'role' => 1,
            'slug' => $slug,
            'confirm' => $pwd,
        ]);

        $newUser->isAdminRegistered = 1;
        $newUser->save();

        if ($newUser) {
            DB::table('invited_companies')->insert(
                ['cmp_name' => $comp_name, 'pwd' => $pwd, 'email' => $email, 'created_at' => new \DateTime()]
            );

            return $newUser;
        }
        return null;
    }

    /**
     * Check if vacancy exists and add it if not, takes vacancy object with company name
     *
     * @param \ $parsed_vacancy
     * @return void
     */
    public function createVacancy($parsed_vacancy)
    {
        // Check if vacancy exist
        $existVacancy = Vacancy::where('hash_sum', '=', $parsed_vacancy['hash_sum'])->first();
        if ($existVacancy != null) {
            // Check if vacancy should be removed
            if ($parsed_vacancy['status'] == 'archive') {
                $existVacancy->delete();
            }

            return;
        }
        // If not - create new
        if ($parsed_vacancy['status'] != 'archive') {
            $vacancy = new Vacancy();
            $vacancy->city = $parsed_vacancy['city'];
            $vacancy->main_technology = $parsed_vacancy['main_technology'];
            $vacancy->short_descr = $parsed_vacancy['short_description'];
            $vacancy->seniority_level = $parsed_vacancy['senior_level'];
            $vacancy->additional_level = $parsed_vacancy['additional_level'];
            $company_id = $this->createCompany($parsed_vacancy['company_name']);
            if ($company_id != null) {
                $vacancy->vacancy_status = "0";
                $vacancy->href_on_vac = $parsed_vacancy['link'];
                $vacancy->company_id = $company_id;
                $vacancy->hash_sum = $parsed_vacancy['hash_sum'];
                $vacancy->slug = SlugService::createSlug(Vacancy::class, 'slug', $vacancy->main_technology, ['unique' => false]) . '-' . hash('crc32', $parsed_vacancy['hash_sum']);

                if ($vacancy->save()) {
                    return;

                }
                return;
            }
        }
        return;
    }
}
