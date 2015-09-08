<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Mail;
use App\Email as Email;
// use GuzzleHttp\Client as Client;
// use GuzzleHttp\Promise;
// use GuzzleHttp\Psr7\Request;

use Validator;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class EmailController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {

        // error_log("------------------------------------------------------");

        $Mailer = new Email;
        $Mailer->email = $request['email'];


        /* $validator class with method fails() */
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        /* $isNewEmail =
                true: no email found in db
                false: duplicate email found in db
        */

        $isNewEmail = (collect($Mailer->where('email', $Mailer->email)->get())
                        ->isEmpty()) ? true : false;

        if ($validator->fails()) {

            error_log(json_encode([ 'error' => [ 'invalid_email' => $request->all() ]]));
            return response()->json([ 'error' => 'E-mail is invalid'])
                             ->setCallback($request->input('callback'));

        } elseif (!$isNewEmail) {

            error_log(json_encode([ 'error' => [ 'duplicate_email' => $request->all() ]]));
            return response()->json([ 'error' => 'E-mail is marked as being subscribed'])
                             ->setCallback($request->input('callback'));

            return redirect('/')->withErrors($validator)->withInput();

        } else {

            error_log(json_encode([ 'mailer' => [ 'newEmail' => $Mailer ]]));

            // soon
            $Scribe = $this->subscribe($Mailer->email); // soon
            error_log(json_encode(['scribe' => $Scribe]));
            $Mailer->save();

            return response()->json([ 'success' => true ])
                             ->setCallback($request->input('callback'));


        }

    }


    /**
     * susbscribe valid, unique $request['email'] to
     * boston meshnet mailing list -- thank you finn!
     **/
    public function subscribe($email)
    {

        $base_uri = 'https://lists.projectmesh.net/cgi-bin/mailman/subscribe/boston';

        $client = new \Guzzle\Service\Client([
            'base_uri' => 'https://lists.projectmesh.net/cgi-bin/mailman/subscribe/boston',
            'timeout'  => 7,
        ]);

        $randStrng = str_random(12);
        error_log(json_encode(['$randStrng' => $randStrng]));

        $request = $client->post($base_uri, [
                        'content-type' => 'application/x-www-form-urlencoded'
                    ],[]);

        $data = [
            'email'   => $email,
            'pw'      => $randStrng,
            'pw-conf' => $randStrng,
            'digest'  => 0,
            'email-button' => 'Subscribe',
        ];

        $request->setBody($data);
        $response = $request->send();

        /* Pending mailserver install..

        $data = (object) [
                    'lists'   => 'boston-join@lists.projectmesh.net',
                    'lists_public' => 'boston@lists.projectmesh.net',
                    'from'    => $email,
                    'subject' => 'Mailing List Request from Boston Meshnet (via Bostonmesh.net Website)',
                    'artisan' => 'igel@hyperboria.ca',
                ] ;

        $success = Mail::send('emails.subscribe', [ 'data' => $data ], function ($message) use($data) {
            $message->from($data->from);
            $message->to($data->lists)
                        ->cc($data->artisan)
                        ->cc($data->from)
                        ->subject($data->subject);
        });

        error_log(json_encode(['subscribe' => $success]));
        */

        $success = true;
        return [ 'success' => $success ];

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
