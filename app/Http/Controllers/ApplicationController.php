<?php


namespace App\Http\Controllers;


use App\Models\Application;

class ApplicationController extends Controller
{
    /**
     * List all applications.
     *
     * @return string
     */
    public function index()
    {
        return self::respond(Application::all());
    }

    /**
     * @param $applicationId
     * @return string
     */
    public function show($applicationId)
    {
        $application = Application::find($applicationId);
        return $application ?
            self::respond($application) :
            self::respond(null, false, 'Application not found.');
    }

    /**
     * @param Request $request
     * @return false|string
     */
    public function store(Request $request)
    {
        try
        {
            self::validateInput($request);
            return self::respond(self::createApplication($request));
        } catch (\Exception $e)
        {
            return self::respond(null, false, $e->getMessage());
        }

    }

    /**
     * @param $applicationId
     * @return false|string
     */
    public function destroy($applicationId)
    {
        $application = Application::find($applicationId);
        if (!$application) return self::respond(null, false, 'Application not found');
        $application->delete();
        return self::respond('Application deleted successfully.');
    }

    /**
     * Downloads the attachment of the application.
     *
     * @param $applicationId
     * @return false|string
     */
    public function downloadCV($applicationId)
    {
        try
        {
            $application = Application::find($applicationId);
            if (!$application) throw new \Exception('Application not found');
            return Storage::download($application->attachment_path);
//            return response()->download(public_path('storage/' . $application->attachment_path));
        } catch (\Exception $e)
        {
            return self::respond(null, false, $e->getMessage());
        }
    }


    /**
     * @param $data
     * @param bool $success
     * @param null $error
     * @return false|string
     */
    private static function respond($data, $success = true, $error = null)
    {
        $response = ['success' => $success, 'payload' => $data, 'error' => $error];
        return json_encode($response);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Validation\Validator
     */
    private static function validateInput(Request $request)
    {
        $request->validate([
            'job_post_id'     => 'required|integer',
            'first_name'      => 'required|string',
            'last_name'       => 'required|string',
            'university_name' => 'required|string',
            'date_of_birth'   => 'required|date',
            'email'           => 'required|email',
            'cv'              => 'required|mimes:doc,docx,pdf,txt|max:2048',
            'notes'           => 'required|string',
        ]);
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws \Exception
     */
    private static function createApplication(Request $request)
    {
        $attachmentPath = self::storeFile($request);
        return Application::create([
            'user_id'         => auth()->user()->id,
            'job_post_id'     => $request->job_post_id,
            'first_name'      => $request->first_name,
            'last_name'       => $request->last_name,
            'university_name' => $request->university_name,
            'date_of_birth'   => Carbon::parse($request->date_of_birth),
            'email'           => $request->email,
            'notes'           => $request->notes,
            'attachment_path' => $attachmentPath
        ]);
    }

    /**
     * @param Request $request
     * @return false|string|null
     * @throws \Exception
     */
    private static function storeFile(Request $request)
    {
        $attachmentPath = null;
        if ($request->hasFile('cv'))
        {
            $file = $request->file('cv');
            $path = 'public/documents';
            $name = $file->getClientOriginalName();
            $attachmentPath = $file->storeAs($path, $name);
        }
        if (!$attachmentPath) throw new \Exception("No file found");
        return $attachmentPath;
    }
}
