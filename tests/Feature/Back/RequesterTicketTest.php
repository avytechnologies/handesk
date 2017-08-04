<?php

namespace Tests\Feature;

use App\Notifications\TicketAssigned;
use App\Notifications\TicketCreated;
use App\Team;
use App\Ticket;
use App\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class RequesterTicketTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function a_ticket_can_be_accessed_by_the_requester_with_ticket_public_token(){
           factory(Ticket::class)->create(["title" => "A public request", "public_token" => "A_PUBLIC_TOKEN"]);

           $response = $this->get("requester/tickets/A_PUBLIC_TOKEN");

           $response->assertStatus(Response::HTTP_OK);
           $response->assertSee("A public request");
    }

    /** @test */
    public function a_requester_can_comment_a_ticket(){
        $ticket = factory(Ticket::class)->create(["public_token" => "A_PUBLIC_TOKEN"]);

        $response = $this->post("requester/tickets/A_PUBLIC_TOKEN/comments", ["body" => "new comment"]);

        $response->assertStatus(Response::HTTP_FOUND);
        $this->assertCount(1, $ticket->fresh()->comments);
        $this->assertEquals(Ticket::STATUS_NEW, $ticket->fresh()->status);
    }

    /** @test */
    public function a_requester_can_comment_and_solve_a_ticket(){
        $ticket = factory(Ticket::class)->create(["public_token" => "A_PUBLIC_TOKEN"]);

        $response = $this->post("requester/tickets/A_PUBLIC_TOKEN/comments", ["body" => "new comment", "solved" => true]);

        $response->assertStatus(Response::HTTP_FOUND);
        $this->assertCount(1, $ticket->fresh()->comments);
        $this->assertEquals(Ticket::STATUS_SOLVED, $ticket->fresh()->status);
    }
}