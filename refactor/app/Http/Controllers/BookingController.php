<?php

namespace DTApi\Http\Controllers;

use DTApi\Models\Job;
use DTApi\Http\Requests;
use DTApi\Models\Distance;
use Illuminate\Http\Request;
use DTApi\Repository\BookingRepository;
use DTApi\Http\Requests\JobStoreRequest;
use DTApi\Repository\NotificationRepository;

/**
 * Class BookingController
 * @package DTApi\Http\Controllers
 */
class BookingController extends Controller
{

    /**
     * @var BookingRepository
     */
    protected $repository;
    protected $notificationRepository;

    /**
     * BookingController constructor.
     * @param BookingRepository $bookingRepository
     */
    public function __construct(BookingRepository $bookingRepository, NotificationRepository $notificationRepository )
    {
        $this->repository = $bookingRepository;
        $this->notificationRepository = $notificationRepository;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        try{
            if($user_id = $request->get('user_id')) {
                $response = $this->repository->getUsersJobs($user_id);
            }
            elseif(auth()->user()->hasRole('admin') || auth()->user()->hasRole('super_admin'))
            {
                $response = $this->repository->getAll($request);
            }
            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'success',
                'data' => $response
            ],200);
        }catch(\Exception $e){
            //Log Error On file 
            // Or Enter that error on errors table
            return response()->json([
                'status' => 'error' ,
                'code' => 500,
                'message' => 'Something Went Wrong',
            ],500);
        }
    }

    /**
     * @param $id
     * @return mixed
     */
    public function show($id)
    {
        try{
            $job = Job::where('id',$id)->with('translatorJobRel.user')->first();
            if($job){
                return response()->json([
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'success',
                    'data' => $job
                ],200);
            }else{
                return response()->json([
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'Job Not Found',
                    'data' => $job
                ],400);
            }
        }catch(\Exception $e){
            //Log Error On file 
            // Or Enter that error on errors table
            return response()->json([
                'status' => 'error' ,
                'code' => 500,
                'message' => 'Something Went Wrong',
            ],500);
        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    
    public function store(JobStoreRequest $request)
    {
        try{
            // $data = $request->all();
            $data = [
                //only pass required fields,
                // No extraa fields shoul be passed to store
            ];

            $response = $this->repository->store(auth()->user(), $data);
            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'success',
                'data' => $response
            ],200);

        } catch(\Exception $e){
            //Log Error On file 
            // Or Enter that error on errors table
            return response()->json([
                'status' => 'error' ,
                'code' => 500,
                'message' => 'Something Went Wrong',
            ],500);
        }
    }

    /**
     * @param $id
     * @param Request $request
     * @return mixed
     */
    public function update($id, Request $request)
    {
        try{
            // $data = $request->all();
            $data = ['validated and required fields'];
            $cuser = auth()->user();
            $response = $this->repository->updateJob($id, array_except($data, ['_token', 'submit']), $cuser);
            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'success',
                'data' => $response
            ],200);
        }catch(\Exception $e){
             //Log Error On file 
            // Or Enter that error on errors table
            return response()->json([
                'status' => 'error' ,
                'code' => 500,
                'message' => 'Something Went Wrong',
            ],500);
        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function immediateJobEmail(Request $request)
    {


        try{
            $adminSenderEmail = config('app.adminemail');
            $data = $request->all();
            $response = $this->repository->storeJobEmail($data);
            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'success',
                'data' => $response
            ],200);
        }catch(\Exception $e){
               //Log Error On file 
            // Or Enter that error on errors table
            return response()->json([
                'status' => 'error' ,
                'code' => 500,
                'message' => 'Something Went Wrong',
            ],500);
        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function getHistory(Request $request)
    {

        try{
            if($user_id = $request->get('user_id')) {
                $response = $this->repository->getUsersJobsHistory($user_id, $request);
                return response()->json([
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'success',
                    'data' => $response
                ],200);
            }else{
                return response()->json([
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'History Not Found',
                ],404);
            }
        }catch(\Exception $e){
            //Log Error On file 
            // Or Enter that error on errors table
            return response()->json([
                'status' => 'error' ,
                'code' => 500,
                'message' => 'Something Went Wrong',
            ],500);
        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function acceptJob(Request $request)
    {
        try{
            $data = $request->all();
            $user = auth()->user()->id;
            $response = $this->repository->acceptJob($data, $user);
            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'success',
                'data' => $response
            ],200);
        }catch(\Exception $e){
             //Log Error On file 
            // Or Enter that error on errors table
            return response()->json([
                'status' => 'error' ,
                'code' => 500,
                'message' => 'Something Went Wrong',
            ],500);
        }
    }

    public function acceptJobWithId(Request $request)
    {
        try{
            $data = $request->get('job_id');
            $user = $request->__authenticatedUser;
            $response = $this->repository->acceptJobWithId($data, $user);
            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'success',
                'data' => $response
            ],200);

        }catch(\Exception $e){
              //Log Error On file 
            // Or Enter that error on errors table
            return response()->json([
                'status' => 'error' ,
                'code' => 500,
                'message' => 'Something Went Wrong',
            ],500);
        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function cancelJob(Request $request)
    {

        try{
            $data = $request->all();
            $user = $request->__authenticatedUser;
            $response = $this->repository->cancelJobAjax($data, $user);
            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'success',
                'data' => $response
            ],200);
        }catch(\Exception $e){
            //Log Error On file 
            // Or Enter that error on errors table
            return response()->json([
                'status' => 'error' ,
                'code' => 500,
                'message' => 'Something Went Wrong',
            ],500);
        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function endJob(Request $request)
    {

        try{
            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'success',
                'data' => $response
            ],200);
        }catch(\Exception $e){
            return response()->json([
                'status' => 'error' ,
                'code' => 500,
                'message' => 'Something Went Wrong',
            ],500);
        }


        $data = $request->all();

        $response = $this->repository->endJob($data);

        return response($response);

    }

    public function customerNotCall(Request $request)
    {

        try{
            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'success',
                'data' => $response
            ],200);
        }catch(\Exception $e){
            return response()->json([
                'status' => 'error' ,
                'code' => 500,
                'message' => 'Something Went Wrong',
            ],500);
        }

        $data = $request->all();

        $response = $this->repository->customerNotCall($data);

        return response($response);

    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function getPotentialJobs(Request $request)
    {
        try{
            $data = $request->all();
            $user = auth()->user()->id;
            $response = $this->repository->getPotentialJobs($user);

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'success',
                'data' => $response
            ],200);
        }catch(\Exception $e){
            return response()->json([
                'status' => 'error' ,
                'code' => 500,
                'message' => 'Something Went Wrong',
            ],500);
        }
    }

    public function distanceFeed(Request $request)
    {
        try{
            
            $data = $request->all();
            $distanceCalculated = $this->repository->getDistance($data);
            if($distanceCalculated){
                return response()->json([
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'Record updated!',
                ],200);
            }else{
                return response()->json([
                    'status' => 'error',
                    'code' => 500,
                    'message' => 'Record Not Updated',
                ],500);
            }
        }catch(\Exception $e){
            return response()->json([
                'status' => 'error' ,
                'code' => 500,
                'message' => 'Something Went Wrong',
            ],500);
        }
    }

    public function reopen(Request $request)
    {
        try{
            $data = $request->all();
            $response = $this->repository->reopen($data);
            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'success',
                'data' => $response
            ],200);
        }catch(\Exception $e){
            return response()->json([
                'status' => 'error' ,
                'code' => 500,
                'message' => 'Something Went Wrong',
            ],500);
        }
    }

   
}
