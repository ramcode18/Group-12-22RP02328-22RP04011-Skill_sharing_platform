@extends('dashboard')

@section('content')
    <h2>Requested Skills</h2>

    @if($requestedSkills->isEmpty())
        <p>You haven’t requested any skills yet. <a href="#">Request a skill</a> to get started!</p>
    @else
        <div class="card">
            <ul>
                @foreach($requestedSkills as $skill)
                    <li>
                        <strong>{{ $skill->title }}</strong> - {{ $skill->description }}
                        <br>
                        <small>Requested on: {{ $skill->created_at->format('M d, Y') }}</small>
                    </li>
                    <hr>
                @endforeach
            </ul>
        </div>
    @endif
@endsection
