<!DOCTYPE html>
<html>

<head>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @page {
            margin: 0;
        }
    </style>
</head>

<body class="flex items-center justify-center min-h-screen bg-white">
    <div class="certificate-container relative w-full max-w-4xl h-[1122px]">
        <img src="{{ asset('certificates/y.png') }}" alt="Certificate Background" class="w-full h-full object-cover">
        <div class="content absolute top-0 left-0 w-full h-full flex flex-col items-center justify-center text-center p-8 text-gray-800">
            <div class="label text-xl mb-2 font-semibold">THIS IS TO CERTIFY THAT</div>
            <div class="name text-4xl font-bold font-serif mt-4">{{ $learner }}</div>
            <div class="label text-lg mt-6 font-semibold">Has successfully completed the course of</div>
            <div class="course text-3xl font-bold mt-3 w-[550px]">{{ $courseTitle }}</div>
            <div class="label text-lg mt-3 font-semibold">and has been awarded with this certificate on</div>
            <div class="date text-lg mt-2">{{ \Carbon\Carbon::parse($date)->format('F j, Y') }}</div>
            <div class="code text-lg font-serif mt-3 text-gray-600">Certificate code : {{ $code }}</div>
        </div>
    </div>
    <button class="download-button mt-6 bg-blue-600 text-white px-6 py-2 rounded-full font-semibold hover:bg-blue-700" onclick="downloadPDF()">Download PDF</button>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script>
        function downloadPDF() {
            const element = document.querySelector('.certificate-container');
            const options = {
                margin: 0,
                filename: 'certificate.pdf',
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2, useCORS: true, width: 794, height: 1122 }, // A4 dimensions in pixels (96dpi)
                jsPDF: { unit: 'in', format: 'a4', orientation: 'portrait', width: 8.27, height: 11.69 }
            };
            html2pdf().from(element).set(options).save();
        }
    </script>
</body>

</html>
