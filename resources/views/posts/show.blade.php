@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div>
            <a href="{{ route('home') }}">Back</a>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body post-create-body">
                    
                    <div>
                        {{ $post->title }}
                        @for ($i = 0; $i<count($texts) + count($images); $i++)
                        @for ($j = 0; $j<count($texts); $j++)
                            @if ( $texts[$j]->position == $i)
                                <div>
                                    {{ $texts[$j]->content }}
                                </div>
                            @endif
                        @endfor
                        @for ($j = 0; $j<count($images); $j++)
                            @if ( $images[$j]->position == $i)
                                <div class="postimg-div">
                                    <div class="postimg-div">
                                        <img class="post-img" src="{{ asset("storage/".$images[$j]->url)}}" alt="">
                                    </div>
                                </div>
                                
                            @endif
                        @endfor

                    @endfor                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection