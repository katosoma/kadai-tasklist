@extends('layouts.app')

@section('content')

<!-- ここにページ毎のコンテンツを書く -->


    <div class="prose ml-4">
        <h2>タスク一覧</h2>
    </div>

    @if (isset($tasks))
        <table class="table table-zebra w-full my-4">
            <thead>
                <tr>
                    <th>id</th>
                    <th>状態</th>
                    <th>タスク</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($tasks as $task)
                <tr>
                    <td><a class="link link-hover text-info" href="{{route('tasks.show', $task->id) }}">{{ $task->id }}</a></td>
                    <td>{{ $task->status }}</td>
                    <td>{{ $task->content }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        {{--ページネーションのリンク--}}
        {{ $tasks->links()}}
    @endif
    
    {{-- メッセージ作成ページへのリンク --}}                                                   {{--追加--}}
    <a class="btn btn-primary" href="{{ route('tasks.create') }}">新規タスクの投稿</a>     　　{{--追加--}}

@endsection