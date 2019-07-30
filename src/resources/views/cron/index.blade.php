@extends('layouts.app', ['title' => __('Cron Management')])

@section('content')
    @include('layouts.headers.simple')

    <div class="container-fluid mt--7">
        <div class="row">
            <div class="col">
                <div class="card shadow">
                    <div class="card-header border-0">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h3 class="mb-0">{{ __('Cron Jobs') }}</h3>
                            </div>
                            <div class="col-4 text-right">
                                <span class="badge badge-primary">{{ config('services.kubernetes.namespace') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        @if (session('status'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('status') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        @if ($errors->any())
                            @foreach ($errors->all() as $error)
                                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                    {{ $error }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endforeach
                        @endif
                    </div>

                    <div class="table-responsive">
                        <table class="table align-items-center table-flush">
                            <thead class="thead-light">
                                <tr>
                                    <th scope="col">{{ __('Name') }}</th>
                                    <th scope="col">{{ __('Schedule') }}</th>
                                    <th scope="col">{{ __('Suspended') }}</th>
                                    <th scope="col">{{ __('Last schedule') }}</th>
                                    <th scope="col"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($crons as $cron)
                                    <tr>
                                        <td>{{ $cron['name'] }}</td>
                                        <td>{{ $cron['schedule'] }}</td>
                                        <td>{{ $cron['suspended'] ? 'Yes' : 'No' }}</td>
                                        <td>{{ $cron['lastScheduleTime'] }}</td>

                                        <td class="text-right">
                                            <div class="dropdown">
                                                <a class="btn btn-sm btn-icon-only text-light" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                                    @if ($cron['suspended'])
                                                        <form action="{{ route('cron.update', $cron['name']) }}" method="post">
                                                            @csrf
                                                            @method('patch')

                                                            <input type="hidden" name="suspend" value="0">

                                                            <button type="button" class="dropdown-item" onclick="confirm('{{ __("Are you sure you want to activate this cron job?") }}') ? this.parentElement.submit() : ''">
                                                                {{ __('Activate') }}
                                                            </button>
                                                        </form>
                                                    @else
                                                        <form action="{{ route('cron.update', $cron['name']) }}" method="post">
                                                            @csrf
                                                            @method('patch')

                                                            <input type="hidden" name="suspend" value="1">

                                                            <button type="button" class="dropdown-item" onclick="confirm('{{ __("Are you sure you want to deactivate this cron job?") }}') ? this.parentElement.submit() : ''">
                                                                {{ __('Deactivate') }}
                                                            </button>
                                                        </form>
                                                    @endif
                                                    <form action="{{ route('cron.trigger', $cron['name']) }}" method="post">
                                                        @csrf
                                                        @method('post')

                                                        <button type="button" class="dropdown-item" onclick="confirm('{{ __("Are you sure you want to trigger this cron job?") }}') ? this.parentElement.submit() : ''">
                                                            {{ __('Trigger') }}
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer py-4">
                        @include('cron.partials.pagination', ['pages' => $pages, 'current' => $current])
                    </div>
                </div>
            </div>
        </div>

        @include('layouts.footers.auth')
    </div>
@endsection
