@extends('layouts.app')

    @section('title')
        Create Post
    @endsection

    @section('content')
 
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
 

    <form method="POST" action={{route('posts.store')}} >
      
      @csrf {{-- csrf = Security code --}}

      <div class="mb-3">
        <label class="form-label">Title</label>
        <input name="title" type="text" class="form-control" value="{{old('title')}}">
      </div>
      <div class="mb-3">
        <label class="form-label">Description</label>
        <textarea name="description" class="form-control" rows="3">{{old('description')}}</textarea>
        </div>
        <br>
        <button class="btn btn-success">Submit</button>

    </form>
            @endsection