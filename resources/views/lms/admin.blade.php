@extends('home.default')
@vite(['resources/css/app.css', 'resources/js/app.js'])
@section('content')


        @include('lms.sidebar')

        <main 
        class="flex-1 p-4 overflow-auto h-screen transition-all duration-300"
        :style="isSidebarOpen ? 'margin-left: 210px;' : 'margin-left: 0;'">

        <div id="dashboardPanel" class="panel">
            @include('lms.dashboard')
        </div>

        <div id="createCoursePanel" class="panel hidden">
            @include('lms.createCourse')
        </div>

        <div id="editCoursePanel" class="panel hidden">
            @include('lms.editCourse')
        </div>

        <div id="createSectionPanel" class="panel hidden">
            @include('lms.createSection')
        </div>

        <div id="connectSectionPanel" class="panel hidden">
            @include('lms.connectSection')
        </div>

        <div id="removeSectionPanel" class="panel hidden">
            @include('lms.removeSection')
        </div>

        <div id="editSectionPanel" class="panel hidden">
            @include('lms.editSection')
        </div>

        <div id="createPracticePanel" class="panel hidden">
            @include('lms.createPractice')
        </div>

        <div id="practiceQuestionPanel" class="panel hidden">
            @include('lms.practiceQuestion')
        </div>

        <div id="editPracticeQuestionPanel" class="panel hidden">
            @include('lms.editPracticeQuestion')
        </div>

        <div id="createMockPanel" class="panel hidden">
            @include('lms.createMock')
        </div>

        <div id="mockOneQuestionPanel" class="panel hidden">
            @include('lms.mockOneQuestion')
        </div>

        <div id="mockSecondQuestionPanel" class="panel hidden">
            @include('lms.mockSecondQuestion')
        </div>

        <div id="editMockOneQuestionPanel" class="panel hidden">
            @include('lms.editMockOneQuestion')
        </div>

        <div id="editMockSecondQuestionPanel" class="panel hidden">
            @include('lms.editMockSecondQuestion')
        </div>

        <div id="headerFooterPanel" class="panel hidden">
            @include('lms.headerFooter')
        </div>

        <div id="googleTagIDPanel" class="panel hidden">
            @include('lms.googleTagID')
        </div>

        <div id="assignCoursePanel" class="panel hidden">
            @include('lms.learnerAssign')
        </div>

        <div id="assignLearnerPanel" class="panel hidden">
            @include('lms.assignCourse')
        </div>

        <div id="bookingsPanel" class="panel hidden">
            @include('lms.bookings')
        </div>
        <div id="blogsPanel" class="panel hidden">
            @include('lms.createBlog')
        </div>
    </main>
</div>

    
@endsection