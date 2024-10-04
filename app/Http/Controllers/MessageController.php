<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Project;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function store(Request $request, Project $project, Message $message)
    {
        // Validate the request
        $request->validate([
            'content' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:messages,id', // Validate parent_id
        ]);

        // Create a new message
        $message = new Message();
        $message->content = $request->content;
        $message->user_id = auth()->id(); // Assuming you have user authentication
        $message->project_id = $project->id; // Associate with the project
        $message->parent_id = $request->parent_id; // Set the parent_id if available
        $message->save();

        // Redirect back to the project show page with a success message
        return redirect()->route('projects.show', [$project, 'tab' => 'messages'])->with('success', 'Message added successfully.');
    }


    public function reply(Request $request, Project $project, Message $message)
    {
        // Validate the request
        $request->validate([
            'reply_content' => 'required|string|max:255',
        ]);

        // Create a new reply
        $reply = new Message();
        $reply->content = $request->reply_content;
        $reply->user_id = auth()->id(); // Assuming you have user authentication
        $reply->project_id = $project->id; // Associate with the project
        $reply->parent_id = $message->id; // Associate with the parent message
        $reply->save();

        // Redirect back to the project show page with the selected message ID and a success message
        return redirect()->route('projects.show', [
            'project' => $project->id,
            'tab' => 'messages',
            'selectedMessageId' => $message->id // Pass the selected message ID
        ])->with('success', 'Reply added successfully.');
    }


}
