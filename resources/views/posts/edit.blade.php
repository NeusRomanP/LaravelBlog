@extends('layouts.app')

@section('content')
<div class="container">
    <div>
        <a href="{{ route('home') }}">Back</a>
    </div>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body post-create-body">

                    @if (Auth::user()->email == env('ADMIN_MAIL'))
                        
                        <div>
                            <h1 class="center-text">Hello Admin!</h1>
                            <form method="POST" action="post" enctype="multipart/form-data">
                                @csrf
                                <div id="post">
                                
                                    <div class="center-text">
                                        <label for="title" >Post title</label>
                                        <br>
                                        <input type="text" name="title" id="title" value="{{ $post->title }}">
                                    </div>
                                    @for ($i = 0; $i<count($texts) + count($images); $i++)
                                        @for ($j = 0; $j<count($texts); $j++)
                                            @if ( $texts[$j]->position == $i)
                                                <div class="post-externalDiv">
                                                    <div class="post-text" >
                                                        <textarea name="" id="{{ $j }}" class="post-element">{{ $texts[$j]->content }}</textarea>
                                                    </div>
                                                    <img class="remove-text-img" src="{{ asset("img/trash-alt-solid.svg") }}" alt="">
                                                </div>
                                            @endif
                                        @endfor
                                        @for ($j = 0; $j<count($images); $j++)
                                            @if ( $images[$j]->position == $i)
                                                <div class="post-externalDiv">
                                                    <div class="post-div">
                                                        <input type="file" name="" id={{ "imginput".$i }} accept="image/*">
                                                        <div class="postimg-div">
                                                            <img class="post-img post-element" src="{{ asset("storage/".$images[$j]->url)}}" alt="" id={{$i }}>
                                                        </div>
                                                    </div>
                                                    
                                                    <img class="remove-text-img" src="{{ asset("img/trash-alt-solid.svg") }}" alt="">
                                                </div>
                                            @endif
                                        @endfor
                                    @endfor
                                </div>                      
                                <div class="add-buttons">
                                    <a id="addtext-button">Add text</a> <a id="addimg-button">Add image</a>
                                </div>
                                
                            </form>
                            <button id="post-button">Post</button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script src="../../js/edit.js"></script>
@endpush
@endsection