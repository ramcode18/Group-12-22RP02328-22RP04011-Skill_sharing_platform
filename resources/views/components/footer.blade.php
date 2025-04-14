<footer class="bg-white border-t border-gray-200">
    <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div class="col-span-2">
                <h3 class="text-sm font-semibold text-gray-400 tracking-wider uppercase">About Us</h3>
                <p class="mt-4 text-base text-gray-500">
                    RamCode App is a platform for sharing and discovering skills. Connect with other learners, share your expertise, and grow together.
                </p>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-gray-400 tracking-wider uppercase">Quick Links</h3>
                <ul class="mt-4 space-y-4">
                    <li>
                        <a href="{{ route('skills.explore') }}" class="text-base text-gray-500 hover:text-gray-900">
                            Explore Skills
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('skills.shared') }}" class="text-base text-gray-500 hover:text-gray-900">
                            Shared Skills
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('skills.requested') }}" class="text-base text-gray-500 hover:text-gray-900">
                            Requested Skills
                        </a>
                    </li>
                </ul>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-gray-400 tracking-wider uppercase">Contact</h3>
                <ul class="mt-4 space-y-4">
                    <li class="text-base text-gray-500">
                        <i class="fas fa-envelope"></i> support@ramcode.com
                    </li>
                    <li class="text-base text-gray-500">
                        <i class="fas fa-phone"></i> +250 788 123 456
                    </li>
                    <li class="text-base text-gray-500">
                        <i class="fas fa-map-marker-alt"></i> Kigali, Rwanda
                    </li>
                </ul>
            </div>
        </div>
        <div class="mt-8 border-t border-gray-200 pt-8">
            <p class="text-base text-gray-400 text-center">
                © {{ date('Y') }} RamCode App. All rights reserved.
            </p>
        </div>
    </div>
</footer>
