<?php

namespace App\Http\Controllers;

use App\Http\Requests\CronRequest;
use Illuminate\Support\Facades\Hash;
use Maclof\Kubernetes\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maclof\Kubernetes\Models\CronJob;
use Maclof\Kubernetes\Models\Job;
use Carbon\Carbon;
use Str;

class CronController extends Controller
{
    /**
     * Display a listing of the users
     *
     * @param  \App\Cron  $model
     * @return \Illuminate\View\View
     */
    public function index(Client $client, Request $request)
    {
        $jobs = collect($client->cronJobs()->find())->map(function ($job) {
            $suspended = ($_ = $job->getJsonPath('$.spec.suspend')->first()) ? $_ : false;

            return [
                "name" => $job->getMetadata('name'),
                "schedule" => ($_ = $job->getJsonPath('$.spec.schedule')->first()) ? $_ : "Undefined",
                "suspended" => $suspended,
                "lastScheduleTime" => ($_ = $job->getJsonPath('$.status.lastScheduleTime')->first()) ? Carbon::createFromFormat('Y-m-d\TH:i:s\Z', $_)->format('d/m/Y H:i:s e') : "Never"
            ];
        })->chunk(15);

        $page = $request->input('page', 1);
        $total_pages = count($jobs);

        if ($page > $total_pages) {
            abort(404);
        }

        return view('cron.index', [
            'crons' => $jobs[$page - 1],
            'pages' => $total_pages,
            'current' => $page
        ]);
    }

    /**
     * Show the form for creating a new user
     *
     * @return \Illuminate\View\View
     */
    private function create()
    {
        return view('users.create');
    }

    /**
     * Store a newly created user in storage
     *
     * @param  \App\Http\Requests\CronRequest  $request
     * @param  \App\Cron  $model
     * @return \Illuminate\Http\RedirectResponse
     */
    private function store(CronRequest $request, Cron $model)
    {
        $model->create($request->merge(['password' => Hash::make($request->get('password'))])->all());

        return redirect()->route('users.index')->withStatus(__('User successfully created.'));
    }

    /**
     * Show the form for editing the specified user
     *
     * @param  \App\Cron  $user
     * @return \Illuminate\View\View
     */
    private function edit(Cron $user)
    {
        return view('cron.edit', compact('user'));
    }

    /**
     * Update the specified user in storage
     *
     * @param  \App\Http\Requests\CronRequest  $request
     * @param  \App\Cron  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update($name, Client $client, CronRequest $request)
    {
        $validator = Validator::make($request->all(), $request->rules());

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        if (!$client->cronJobs()->exists($name)) {
            abort(404);
        }

        $cronjob = new CronJob([
            'metadata' => [
                'name' => $name
            ],
            'spec' => [
                'suspend' => !!$request->suspend
            ]
        ]);

        $client->cronJobs()->patch($cronjob);

        return back()->withStatus(__('Cron job successfully updated.'));
    }

    /**
     * Remove the specified user from storage
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    private function destroy(/*Cron $user*/)
    {
        //$user->delete();

        return redirect()->route('cron.index')->withStatus(__('User successfully deleted.'));
    }

    public function trigger($name, Client $client)
    {
        if (!$client->cronJobs()->exists($name)) {
            abort(404);
        }

        $cronjob_spec = $client->cronJobs()
            ->setFieldSelector([
                'metadata.name' => $name,
            ])
            ->find()
            ->first()
            ->getJsonPath('$.spec.jobTemplate.spec')
            ->first()
            ->data();

        $job = new Job([
            'metadata' => [
                'name' => "{$name}-manual-" . strtolower(Str::random(3)),
                'annotations' => [
                    'cronjob.kubernetes.io/instantiate' => 'manual'
                ]
            ],
            'spec' => array_remove_empty($cronjob_spec),
        ]);

        $client->jobs()->create($job);

        return redirect()->route('cron.index')->withStatus(__('Cron job successfully triggered.'));
    }
}
