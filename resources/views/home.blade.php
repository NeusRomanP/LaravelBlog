@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">

                    @if (Auth::user()->email == env('ADMIN_MAIL'))
                        <h1>Hello Admin!</h1>
                        <a href="{{ route('posts.create') }}">Create a new post</a>
                    @endif

                    @foreach ($posts as $post)
                        <div class="post-container">
                            <div>
                                <a href="{{ route('show', $post->id) }}">
                                    <b>
                                        {{ $post->title }}
                                    </b>
                                </a>
                            </div>
                            
                            <div class="max-3lines justify-text">
                                @if (count($post->texts)>0)
                                    {{ $post->texts[0]->content }}
                                @endif
                            </div>

                            @if (Auth::user()->email == env('ADMIN_MAIL'))
                                <hr>
                                <div>
                                    <a href="{{ route('posts.edit', $post->id) }}">Edit</a> | <a class="red-text" href="{{ route('posts.delete', $post->id)}}">Delete</a>
                                </div>
                            @endif
                            
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
