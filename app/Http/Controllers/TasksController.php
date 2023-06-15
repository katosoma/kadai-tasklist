<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Task; //追加

class TasksController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
     
    //getでtasks/にアクセスされた場合の「一覧表示処理」
    public function index()
    {   $data=[];
        if(\Auth::check()){ //認証済みの場合
            //認証済みユーザーを取得
            $user = \Auth::user();
            //ユーザの投稿の一覧を作成日時の降順で取得
            $tasks=$user->tasks()->orderBy('created_at','desc')->paginate(10);
            $data = [
                'user' => $user,
                'tasks' => $tasks,
            ];
        }
        
        //dashboardビューでそれらを表示
        return view('dashboard', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    
    // getでtasks/createにアクセスされた場合の「新規登録画面表示処理」
    public function create()
    {   
        if(\Auth::check()){ //認証済みの場合
            $task = new Task;
            //タスク作成ビューを表示
            return view('tasks.create', [
                    'task' => $task,
                    ]);
        } else{
            return redirect('/');    
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    
    // postでtasks/にアクセスされた場合の「新規登録処理」 
    public function store(Request $request)
    {   
        // バリデーション
        $request->validate([
            'status' => 'required|max:10',   
            'content' => 'required|max:255',
        ]);
        
        if(\Auth::check()){ //認証済みの場合
            //認証済みユーザ（閲覧者）の投稿として作成（リクエストされた値を元に作成）
            $request->user()->tasks()->create([
                'content' => $request->content,
                'status' => $request->status,
                'user_id' => $request->user()->id, // 正しいユーザIDを指定する
            ]);
        } else{
            return redirect('/');        
        }
        
       // トップページへリダイレクトさせる
        return redirect('/');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    
    // getでtasks/idにアクセスされた場合の「取得表示処理」
    public function show($id)
    {
        // idの値でタスクを検索して取得
        $task = \App\Models\Task::findOrFail($id);

        //認証済みユーザ（閲覧者）がその投稿の所有者である場合タスク詳細ビューでそれを表示
        if (\Auth::id() === $task->user_id){
            return view('tasks.show', [
                'task' => $task,
            ]);
        }else{
            return redirect('/');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
     
    // getでtasks/id/editにアクセスされた場合の「更新画面表示処理」
    public function edit($id)
    {
        //idの値でタスクを検索して取得
        $task = \App\Models\Task::findOrFail($id);
        
        // 認証済みユーザ（閲覧者）がその投稿の所有者である場合はタスク編集ビューでそれを表示
        if (\Auth::id() === $task->user_id){
            return view('tasks.edit', [
                'task' => $task,
            ]);
        }else{
            return redirect('/');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
     
    // putまたはpatchでtasks/idにアクセスされた場合の「更新処理」
    public function update(Request $request, $id)
    {   
        // バリデーション
        $request->validate([
            'status' => 'required|max:10',   // 追加
            'content' => 'required|max:255',
        ]);
        
        //idの値でタスクを検索して取得
        $task = \App\Models\Task::findOrFail($id);
        
        // 認証済みユーザ（閲覧者）がその投稿の所有者である場合は変更を保存
        if (\Auth::id() === $task->user_id){
            $task->status = $request->status;    // 追加
            $task->content = $request->content;
            $task->save();
        }else{
            return redirect('/');
        }
        
        // トップページへリダイレクトさせる
        return redirect('/');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
     
    // deleteでtasks/idにアクセスされた場合の「削除処理」
    public function destroy($id)
    {
        //idの値でタスクを検索して取得
        $task = \App\Models\Task::findOrFail($id);
        
        //認証済みユーザ（閲覧者）がその投稿の所有者である場合は投稿を削除
        if (\Auth::id() === $task->user_id){
            $task->delete();
            return redirect('/')
                ->with('success','Delete Successful');
        }else{
            return redirect('/');
        }

        // トップページへリダイレクトさせる
        return redirect('/')
            ->with('Delete Failed');
    }
}
