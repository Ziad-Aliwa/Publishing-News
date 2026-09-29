@extends('layouts.app')

    @section('title')
        {{-- Edit page --}}
        Edit Post
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


    <form method="POST" action="{{route('posts.update' , $post->id)}}">
      
      @csrf {{-- csrf = Security code --}}
      @method('PUT')

      <div class="mb-3">
        <label class="form-label">Title</label>
        <input name="title" type="text" value="{{$post->title}}" class="form-control">
      </div>
      <div class="mb-3">
        <label class="form-label">Description</label>
        <textarea name="description" class="form-control" rows="3">{{$post->description}}</textarea>
        </div>
        <br>
        <button class="btn btn-primary">Update</button>

    </form>
            @endsection