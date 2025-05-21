<x-error-layout :title="$pageTitle">
    <!-- Error -->
    <div class="container-xxl container-p-y">
        <div class="misc-wrapper">
          <h1 class="mb-2 mx-2" style="line-height: 6rem; font-size: 6rem">403</h1>
          <h4 class="mb-2 mx-2">Page Not Found️ ⚠️</h4>
          <p class="mb-6 mx-2">You don't have permission to access this page.</p>
          <a href="{{$redirectUrl}}" class="btn btn-primary mb-10">Back to home</a>
          <div class="mt-4">
            <img
              src="{{asset('assets/img/illustrations/page-misc-error.png')}}"
              alt="page-misc-error-light"
              width="225"
              class="img-fluid" />
          </div>
        </div>
      </div>
      <div class="container-fluid misc-bg-wrapper">
        <img
          src="{{asset('assets/img/illustrations/bg-shape-image-light.png')}}"
          height="355"
          alt="page-misc-error"
          data-app-light-img="{{asset('assets/img/illustrations/bg-shape-image-light.png')}}"
          data-app-dark-img="{{asset('assets/img/illustrations/bg-shape-image-dark.png')}}" />
      </div>
</x-error-layout>

