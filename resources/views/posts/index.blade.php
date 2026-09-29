@extends('layouts.app')

    @section('title')
        Home
    @endsection

    @section('content')
        
    <table class="table table-dark table-striped mt-4">
            
        <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Title</th>
                    <th scope="col">Posted By</th>
                    <th scope="col">Created At</th>
                    <th scope="col">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($posts as $post)
                {{-- @dd($posts , $post) --}}
                <tr>
                    <td>{{$post->id}}</td>  {{-- or $post->id == becus this is object --}}
                    <td>{{$post['title']}}</td>
                    <td>{{$post->user ? $post->user->name : 'Not found'}}</td>
                    <td>{{$post->created_at->format('Y/m/d')}}</td>
                    <td>
                        <a href="{{route('posts.show' , $post->id)}}" class="btn btn-info">View</a>
                        @auth
                            @if (auth()->user()->hasVerifiedEmail() && $post->user_id === auth()->id())
                                <a href="{{ route('posts.edit', $post->id) }}" class="btn btn-primary">Edit</a>
                                <form style="display: inline;" method="POST" action="{{ route('posts.destroy', $post->id) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">Delete</button>
                                </form>
                            @endif
                        @endauth
                    
                    </td>
                </tr>
                @endforeach    
            </tbody>
        </table>

    @endsection