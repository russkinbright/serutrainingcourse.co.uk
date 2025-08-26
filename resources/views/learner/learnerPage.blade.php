@extends('home.default')

@section('content')
    @vite(['resources/css/app.css', 'resources/js/app.js'])

   

     @include('learner.javascript.course')


    <main x-data="mergedApp()" 
          class="flex-1 overflow-auto h-screen transition-all duration-300"
          :style="isSidebarOpen ? 'margin-left: 220px;' : 'margin-left: 0;'">

        @include('learner.learnerHeader')

        <div id="learnersDashboardPanel" class="panel">
            @include('learner.learnerDashboard')
        </div>

        <div id="learnerCoursePanel" class="panel hidden">
            @include('learner.learnerCourse')
        </div>

        <div id="learnerContinueCourse" class="panel hidden">
            {{-- @include('learner.continueCourse') --}}
        </div>

        <div id="learnerCourseDetails" class="panel hidden">
            @include('learner.courseDetails')
        </div>

        <div id="learnerCourseCertificate" class="panel hidden">
            @include('learner.learnerCertificatePage')
        </div>

        <div id="learnerViewProfile" class="panel hidden">
            @include('learner.viewProfile')
        </div>
    </main>
    
@endsection
