<?php

namespace App\Http\Controllers;

use App\Notes;
use Illuminate\Http\Request;

class NotesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $notes =  Notes::where('user_id', $request->user()->id)->with('user')->orderBy('created_at', 'desc')->get();
    
        return response()->json(['notes' => $notes]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Notes $note)
    {
        //
         $request->validate([
            'content' => 'required',
        ]);

        //Création
        $note = new Notes;
        $note->user_id = $request->user()->id;
        $note->content = $request->content;
        $note->save();
        $note->with('user')->get();

        //Redirection
        return response()->json(['note' => $note]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Notes  $notes
     * @return \Illuminate\Http\Response
     */
    public function show($id, Request $request)
    {
        //
        $note = Notes::findOrFail($id);
        if($note->user_id != $request->user()->id){
            return response(null, 403);
        }
        return response()->json(['note' => $note]);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Notes  $notes
     * @return \Illuminate\Http\Response
     */
    public function edit(Notes $notes)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Notes  $notes
     * @return \Illuminate\Http\Response
     */
    public function update($id, Request $request, Notes $note)
    {
        //
        $request->validate([
            'content' => 'required'
        ]);

        $note = Notes::findOrFail($id);
        if($note->user_id != $request->user()->id){
            return response(null, 403);
        }
        $note->content = $request->content;
        $note->save();

       
        return response()->json(['note' => $note]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Notes  $notes
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Request $request)
    {
        //
        $note = Notes::findOrFail($id);
        if($note->user_id != $request->user()->id){
            return response(null, 403);
        }
        $note->delete();
        return response(null);
        //return response()->json(['message' => 'Note is deleted !']);
    }
}
