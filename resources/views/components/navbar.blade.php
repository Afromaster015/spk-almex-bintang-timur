<nav class="flex flex-row max-w-6xl mx-auto justify-between items-center mt-10">
  <img
    style="width: 18%; height: 18%"
    src="{{ asset('images/Logo Almex Bintang Timur.png') }}"
    alt="logo-almex"
    href="{{ url('/') }}"
  />
  <ul class="flex flex-row gap-x-15 font-semibold">
    <li>
      <a
        href=""
        class="relative pb-1 text-base text-[#ef4343] font-semibold after:content-[''] after:absolute after:left-1/2 after:bottom-0 after:w-0 after:h-[2px] after:bg-[#ef4343] after:transition-all after:duration-300 hover:after:w-full hover:after:left-0"
      >
        Products
      </a>
    </li>
    <li>
      <a
        href=""
        class="relative pb-1 text-base text-[#ef4343] font-semibold after:content-[''] after:absolute after:left-1/2 after:bottom-0 after:w-0 after:h-[2px] after:bg-[#ef4343] after:transition-all after:duration-300 hover:after:w-full hover:after:left-0"
      >
        Pricing
      </a>
    </li>
    <li>
      <a
        href=""
        class="relative pb-1 text-base text-[#ef4343] font-semibold after:content-[''] after:absolute after:left-1/2 after:bottom-0 after:w-0 after:h-[2px] after:bg-[#ef4343] after:transition-all after:duration-300 hover:after:w-full hover:after:left-0"
      >
        Testimonials
      </a>
    </li>
    <li>
      <a
        href=""
        class="relative pb-1 text-base text-[#ef4343] font-semibold after:content-[''] after:absolute after:left-1/2 after:bottom-0 after:w-0 after:h-[2px] after:bg-[#ef4343] after:transition-all after:duration-300 hover:after:w-full hover:after:left-0"
      >
        About
      </a>
    </li>
  </ul>
  <div>
    <button
      class="bg-[#ef4343] text-white text-base px-7 py-3 rounded-full font-bold hover:bg-[#af3535] hover:shadow-lg hover:scale-120 ease-in-out hover: duration-300 cursor-pointer "
      onclick="window.location.href = '{{ url('/login') }}'"
    >
      Sign in
    </button>
  </div>
</nav>
