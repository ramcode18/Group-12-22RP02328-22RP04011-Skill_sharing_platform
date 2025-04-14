@extends('dashboard')

@section('content')
    <h2>Explore Skills</h2>

    @if($allSkills->isEmpty())
        <p>No skills are currently available. Check back later!</p>
    @else
        <div class="card">
            <ul>
                @foreach($allSkills as $skill)
                    <li>
                        <strong>{{ $skill->title }}</strong> - {{ $skill->description }}
                        <br>
                        <small>Shared by: {{ $skill->user->name }}</small>
                    </li>
                    <hr>
                @endforeach
            </ul>
        </div>
    @endif
@endsection
