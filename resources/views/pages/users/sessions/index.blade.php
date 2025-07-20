@extends('layouts.app')

@section('title', __('sessions.title'))

@section('content')
    @if ($sessions->count() > 0)
        <div class="row mb-4">
            <div class="col-md-6">
                <h5>{{ __('sessions.current_session') }}</h5>
                @foreach ($sessions as $session)
                    @if ($session->session_id === session()->getId())
                        <div class="card border-success">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-8">
                                        <h6 class="card-title">
                                            <i class="fas fa-desktop me-2"></i>
                                            {{ __('sessions.device') }}
                                        </h6>
                                        <p class="card-text">
                                            <strong>{{ __('sessions.ip_address') }}:</strong> {{ $session->ip_address }}<br>
                                            <strong>{{ __('sessions.last_activity') }}:</strong>
                                            {{ $session->last_activity->diffForHumans() }}<br>
                                            <strong>{{ __('sessions.user_agent') }}:</strong>
                                            {{ Str::limit($session->user_agent, 100) }}
                                        </p>
                                    </div>
                                    <div class="col-md-4 text-end">
                                        <span class="badge bg-success">{{ __('sessions.current_session') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>

            <div class="col-md-6">
                <h5>{{ __('sessions.other_sessions') }}</h5>
                @php $otherSessions = $sessions->where('session_id', '!=', session()->getId()) @endphp
                @if ($otherSessions->count() > 0)
                    @foreach ($otherSessions as $session)
                        <div class="card border-warning mb-2">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-8">
                                        <h6 class="card-title">
                                            <i class="fas fa-mobile-alt me-2"></i>
                                            {{ __('sessions.device') }}
                                        </h6>
                                        <p class="card-text">
                                            <strong>{{ __('sessions.ip_address') }}:</strong>
                                            {{ $session->ip_address }}<br>
                                            <strong>{{ __('sessions.last_activity') }}:</strong>
                                            {{ $session->last_activity->diffForHumans() }}<br>
                                            <strong>{{ __('sessions.user_agent') }}:</strong>
                                            {{ Str::limit($session->user_agent, 100) }}
                                        </p>
                                    </div>
                                    <div class="col-md-4 text-end">
                                        <form action="{{ route('sessions.terminate', $session->session_id) }}"
                                            method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger"
                                                onclick="return confirm('{{ __('sessions.terminate_confirm') }}')">
                                                <i class="fas fa-times"></i> {{ __('sessions.terminate') }}
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <div class="mt-3">
                        <form action="{{ route('sessions.terminate-others') }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-warning"
                                onclick="return confirm('{{ __('sessions.terminate_all_confirm') }}')">
                                <i class="fas fa-times-circle"></i> {{ __('sessions.terminate_all') }}
                            </button>
                        </form>
                    </div>
                @else
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> {{ __('sessions.no_sessions') }}
                    </div>
                @endif
            </div>
        </div>
    @else
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> {{ __('sessions.no_sessions') }}
        </div>
    @endif
@endsection
