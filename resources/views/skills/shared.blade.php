@extends('dashboard')

@section('content')
    <h2>My Shared Skills</h2>

    @if($skills->isEmpty())
        <p>You haven't shared any skills yet. <a href="#">Click here to add one</a>.</p>
    @else
        <div class="card">
            <ul>
                @foreach($skills as $skill)
                    <li>
                        <strong>{{ $skill->title }}</strong> - {{ $skill->description }}
                        <br>
                        <small>Shared on: {{ $skill->created_at->format('M d, Y') }}</small>
                    </li>
                    <hr>
                @endforeach
            </ul>
        </div>
    @endif
@endsection
