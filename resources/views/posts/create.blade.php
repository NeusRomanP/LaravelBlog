@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body post-create-body">

                    @if (Auth::user()->email == 'admin@gmail.com')
                        <div>
                            <h1 class="center-text">Hello Admin!</h1>
                            <form method="POST" action="post" enctype="multipart/form-data">
                                @csrf
                                <div id="post">
                                
                                    <div class="center-text">
                                        <label for="title" >Post title</label>
                                        <br>
                                        <input type="text" name="title" id="title">
                                    </div>
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
<script src="../../js/create.js"></script>
@endpush
@endsection