<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Mail;
use App\Email as Email;
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
            // $Scribe = $this->subscribe($Mailer->email); // soon

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

       return [ 'success' => $email ];

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
