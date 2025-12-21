@extends('layouts.base-layout')

@section('content')
<div x-data="{ scrolled: false }" @scroll.window="scrolled = window.scrollY > 50">
    {{-- Header --}}
    <header class="bg-header dark:bg-slate-800 sticky top-0 z-30 transition-all duration-300 shadow-sm border-b border-transparent dark:border-slate-700" :class="scrolled ? 'shadow-md' : ''">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <x-ui.logo />
                <x-partials.navigation />
            </div>
        </div>
    </header>

    <main class="bg-main text-gray-800 dark:bg-slate-900 dark:text-gray-100 antialiased font-sans transition-colors duration-300">
        {{-- Section 1: Hero Section (Full Height) --}}
        <section class="min-h-screen flex items-center justify-center bg-gradient-to-b from-[#FAF7F2] to-white dark:from-slate-900 dark:to-slate-800 px-4 sm:px-6 lg:px-8 py-20 sm:py-24 lg:py-32">
            <div class="max-w-7xl mx-auto w-full">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12 items-center">
                    {{-- Content --}}
                    <div class="text-center lg:text-left space-y-6 sm:space-y-8">
                        <h1 class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-serif font-bold text-gray-900 dark:text-white leading-tight">
                            {{__('homepage.experience_the_art_of_focus')}}
                        </h1>
                        <p class="text-lg sm:text-xl md:text-2xl text-gray-600 dark:text-gray-400 max-w-2xl mx-auto lg:mx-0">
                            {{__('homepage.simple_elegant_and_powerful_task_management_for_everyone')}}
                        </p>
                        <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                            <a href="#features" class="inline-flex items-center justify-center px-8 py-4 bg-pink-500 hover:bg-pink-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                                {{__('homepage.discover_now')}}
                                <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </a>
                            <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-8 py-4 bg-white dark:bg-slate-800 border-2 border-gray-300 dark:border-slate-600 hover:border-pink-500 dark:hover:border-pink-500 text-gray-900 dark:text-white font-semibold rounded-xl transition-all duration-300">
                                {{__('homepage.get_started')}}
                            </a>
                        </div>
                    </div>

                    {{-- Visual: Floating Task Card Mockup (Glassmorphism) --}}
                    <div class="relative flex items-center justify-center">
                        <div class="relative w-full max-w-md">
                            {{-- Floating Card --}}
                            <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-3xl p-6 sm:p-8 shadow-2xl border border-white/20 dark:border-slate-700/50 transform rotate-3 hover:rotate-0 transition-transform duration-500" style="background: linear-gradient(135deg, rgba(255,255,255,0.9) 0%, rgba(255,255,255,0.7) 100%);">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-semibold px-3 py-1 rounded-full bg-pink-100 dark:bg-pink-900/30 text-pink-700 dark:text-pink-300">
                                            {{__('category.work')}}
                                </span>
                                        <span class="text-xs font-semibold px-3 py-1 rounded-full bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300 border border-red-200 dark:border-red-800">
                                            {{__('priority.high')}}
                            </span>
                                    </div>
                                    <div class="w-8 h-8 rounded-full bg-pink-200 dark:bg-pink-900/50 flex items-center justify-center">
                                        <svg class="w-4 h-4 text-pink-600 dark:text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                </div>
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">{{__('task.complete_project_proposal')}}</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">{{__('task.finish_writing_the_project_proposal_document_and_submit_to_client')}}</p>
                                <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                                    <span>{{__('task.today_5_00_pm')}}</span>
                                    <span class="px-2 py-1 bg-gray-100 dark:bg-slate-700 rounded-lg">{{__('task.in_progress')}}</span>
                                </div>
                            </div>
                            
                            {{-- Decorative Elements --}}
                            <div class="absolute -top-4 -right-4 w-24 h-24 bg-pink-200 dark:bg-pink-900/30 rounded-full blur-2xl opacity-50"></div>
                            <div class="absolute -bottom-4 -left-4 w-32 h-32 bg-blue-200 dark:bg-blue-900/30 rounded-full blur-2xl opacity-50"></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Section 2: How It Works (Features) --}}
        <section id="features" class="py-20 sm:py-24 lg:py-32 bg-white dark:bg-slate-800 px-4 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-16 sm:mb-20">
                    <h2 class="text-3xl sm:text-4xl md:text-5xl font-serif font-bold text-gray-900 dark:text-white mb-4">
                        {{__('homepage.how_it_works')}}
                    </h2>
                    <p class="text-lg sm:text-xl text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
                        {{__('homepage.powerful_features_designed_to_help_you_stay_organized_and_focused')}}
                    </p>
                </div>

                {{-- Feature 1: Smart Priority --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16 items-center mb-20 sm:mb-24">
                    <div class="order-2 lg:order-1">
                        <div class="space-y-6">
                            <div class="inline-block px-4 py-2 bg-pink-100 dark:bg-pink-900/30 text-pink-700 dark:text-pink-300 rounded-full text-sm font-semibold">
                                {{__('homepage.smart_organization')}}
                            </div>
                            <h3 class="text-3xl sm:text-4xl font-serif font-bold text-gray-900 dark:text-white">
                                {{__('homepage.smart_priority_system')}}
                            </h3>
                            <p class="text-lg text-gray-600 dark:text-gray-400 leading-relaxed">
                                {!! __('homepage.organize_your_tasks_with_our_intelligent_priority_system') !!}
                            </p>
                            <ul class="space-y-3 text-gray-700 dark:text-gray-300">
                                <li class="flex items-start gap-3">
                                    <svg class="w-6 h-6 text-pink-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span>{{__('homepage.visual_priority_indicators_for_quick_recognition')}}</span>
                                </li>
                                <li class="flex items-start gap-3">
                                    <svg class="w-6 h-6 text-pink-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span>{{__('homepage.filter_and_sort_by_priority_level')}}</span>
                        </li>
                                <li class="flex items-start gap-3">
                                    <svg class="w-6 h-6 text-pink-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span>{{__('homepage.automatic_sorting_by_importance')}}</span>
                        </li>
                    </ul>
                        </div>
                    </div>
                    <div class="order-1 lg:order-2">
                        <div class="relative">
                            <img src="https://images.unsplash.com/photo-1484480974693-6ca0a78fb36b?w=800&h=600&fit=crop" alt="Smart Priority System" class="rounded-3xl shadow-2xl w-full h-auto" loading="lazy" decoding="async" />
                            <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent rounded-3xl"></div>
                        </div>
                    </div>
                </div>

                {{-- Feature 2: Focus Mode --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16 items-center mb-20 sm:mb-24">
                    <div>
                        <div class="relative">
                            <img src="https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?w=800&h=600&fit=crop" alt="Focus Mode Pomodoro Timer" class="rounded-3xl shadow-2xl w-full h-auto" loading="lazy" decoding="async" />
                            <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent rounded-3xl"></div>
                        </div>
                    </div>
                    <div>
                        <div class="space-y-6">
                            <div class="inline-block px-4 py-2 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 rounded-full text-sm font-semibold">
                                {{__('homepage.productivity_boost')}}
                            </div>
                            <h3 class="text-3xl sm:text-4xl font-serif font-bold text-gray-900 dark:text-white">
                                {{__('homepage.focus_mode_with_pomodoro_timer')}}
                            </h3>
                            <p class="text-lg text-gray-600 dark:text-gray-400 leading-relaxed">
                                {{__('homepage.stay_focused_and_productive_with_our_built_in_pomodoro_timer')}}
                            </p>
                            <ul class="space-y-3 text-gray-700 dark:text-gray-300">
                                <li class="flex items-start gap-3">
                                    <svg class="w-6 h-6 text-indigo-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span>{{__('homepage.customizable_timer_duration_default_25_minutes')}}</span>
                                </li>
                                <li class="flex items-start gap-3">
                                    <svg class="w-6 h-6 text-indigo-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span>{{__('homepage.pause_and_resume_functionality')}}</span>
                                </li>
                                <li class="flex items-start gap-3">
                                    <svg class="w-6 h-6 text-indigo-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span>{{__('homepage.automatic_completion_suggestion_after_timer_ends')}}</span>
                        </li>
                    </ul>
                        </div>
                    </div>
                </div>

                {{-- Feature 3: Color Tags & Categories --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16 items-center">
                    <div class="order-2 lg:order-1">
                        <div class="space-y-6">
                            <div class="inline-block px-4 py-2 bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 rounded-full text-sm font-semibold">
                                {{__('homepage.visual_organization')}}
                            </div>
                            <h3 class="text-3xl sm:text-4xl font-serif font-bold text-gray-900 dark:text-white">
                                {{__('homepage.color_tags_and_categories')}}
                            </h3>
                            <p class="text-lg text-gray-600 dark:text-gray-400 leading-relaxed">
                                {!! __('homepage.organize_your_tasks_visually_with_custom_color_tags_and_predefined_categories') !!}
                            </p>
                            <ul class="space-y-3 text-gray-700 dark:text-gray-300">
                                <li class="flex items-start gap-3">
                                    <svg class="w-6 h-6 text-purple-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span>{{__('homepage.6_beautiful_pastel_color_options')}}</span>
                                </li>
                                <li class="flex items-start gap-3">
                                    <svg class="w-6 h-6 text-purple-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span>{{__('homepage.predefined_categories_with_icons')}}</span>
                                </li>
                                <li class="flex items-start gap-3">
                                    <svg class="w-6 h-6 text-purple-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                                    <span>{{__('homepage.filter_tasks_by_category_instantly')}}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="order-1 lg:order-2">
                        <div class="relative">
                            <img src="https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=800&h=600&fit=crop" alt="Color Tags and Categories" class="rounded-3xl shadow-2xl w-full h-auto" />
                            <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent rounded-3xl"></div>
                        </div>
                    </div>
                </div>
                {{-- Button Tiếp --}}
                        <div class="flex justify-start pt-8">
                            <a href="#about" class="group inline-flex items-center justify-center gap-3 px-8 py-4 bg-white dark:bg-slate-800 border-2 border-gray-300 dark:border-slate-600 hover:border-pink-500 dark:hover:border-pink-500 text-gray-900 dark:text-white font-semibold rounded-xl transition-all duration-300 shadow-sm hover:shadow-lg transform hover:-translate-y-1">
                                <span>{{__('homepage.next')}}</span>
                                <svg class="w-5 h-5 transform group-hover:translate-y-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </a>
                        </div>
            </div>
        </section>

        {{-- Section 3: About Us --}}
        <section id="about" class="py-20 sm:py-24 lg:py-32 bg-gradient-to-b from-white to-[#FAF7F2] dark:from-slate-800 dark:to-slate-900 px-4 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16 items-center">
                    {{-- Left: Image --}}
                    <div class="relative">
                        <div class="transform rotate-2 hover:rotate-0 transition-transform duration-500">
                            <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=800&h=600&fit=crop" alt="PoLuv Tasks Team" class="rounded-3xl shadow-2xl w-full h-auto" loading="lazy" decoding="async" />
                        </div>
                        <div class="absolute -bottom-6 -right-6 w-32 h-32 bg-pink-200 dark:bg-pink-900/30 rounded-full blur-2xl opacity-50 -z-10"></div>
                    </div>

                    {{-- Right: Content --}}
                    <div class="space-y-6">
                        <div class="inline-block px-4 py-2 bg-pink-100 dark:bg-pink-900/30 text-pink-700 dark:text-pink-300 rounded-full text-sm font-semibold">
                            {{__('homepage.our_story')}}
                        </div>
                        <h2 class="text-3xl sm:text-4xl md:text-5xl font-serif font-bold text-gray-900 dark:text-white">
                            {{__('homepage.we_are_poluv_tasks')}}
                        </h2>
                        <p class="text-lg text-gray-600 dark:text-gray-400 leading-relaxed">
                            {{__('homepage.at_poluv_tasks_we_believe_that_productivity_should_be_peaceful_not_stressful')}}
                        </p>
                        <p class="text-lg text-gray-600 dark:text-gray-400 leading-relaxed">
                            {{__('homepage.we_understand_that_managing_tasks_can_be_overwhelming_which_is_why_weve_designed_poluv_tasks_with_a_focus_on_simplicity_and_user_experience')}}
                        </p>
                        <p class="text-lg text-gray-600 dark:text-gray-400 leading-relaxed">
                            {{__('homepage.join_thousands_of_users_who_have_discovered_the_art_of_focused_productivity_start_your_journey_today_and_experience_the_difference_that_thoughtful_design_can_make')}}
                        </p>
                        {{-- Button Tiếp --}}
                        <div class="flex justify-start pt-8">
                            <a href="#contact" class="group inline-flex items-center justify-center gap-3 px-8 py-4 bg-white dark:bg-slate-800 border-2 border-gray-300 dark:border-slate-600 hover:border-pink-500 dark:hover:border-pink-500 text-gray-900 dark:text-white font-semibold rounded-xl transition-all duration-300 shadow-sm hover:shadow-lg transform hover:-translate-y-1">
                                <span>{{__('homepage.next')}}</span>
                                <svg class="w-5 h-5 transform group-hover:translate-y-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Section 4: Contact & Feedback --}}
        <section id="contact" class="py-20 sm:py-24 lg:py-32 bg-white dark:bg-slate-800 px-4 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-16 sm:mb-20">
                    <h2 class="text-3xl sm:text-4xl md:text-5xl font-serif font-bold text-gray-900 dark:text-white mb-4">
                        {{__('homepage.get_in_touch')}}
                    </h2>
                    <p class="text-lg sm:text-xl text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
                        {{__('homepage.we_d_love_to_hear_from_you_send_us_a_message_or_feedback')}}
                    </p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16">
                    {{-- Left: Contact Info --}}
                    <div class="space-y-8">
                        <div>
                            <h3 class="text-2xl font-serif font-bold text-gray-900 dark:text-white mb-6">
                                {{__('homepage.contact_information')}}
                            </h3>
                            <p class="text-gray-600 dark:text-gray-400 mb-8">
                                {{__('homepage.have_questions_or_suggestions_reach_out_to_us_through_our_social_media_channels_or_send_us_feedback_using_the_form')}}
                            </p>
                        </div>

                        {{-- Social Media Icons --}}
                        <div class="flex flex-wrap gap-4">
                            <a href="https://facebook.com" target="_blank" class="flex items-center justify-center w-12 h-12 rounded-full bg-gray-100 dark:bg-slate-700 hover:bg-pink-100 dark:hover:bg-pink-900/30 text-gray-700 dark:text-gray-300 hover:text-pink-600 dark:hover:text-pink-400 transition-all duration-300 transform hover:scale-110" title="Facebook">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                </svg>
                            </a>
                            <a href="https://github.com" target="_blank" class="flex items-center justify-center w-12 h-12 rounded-full bg-gray-100 dark:bg-slate-700 hover:bg-pink-100 dark:hover:bg-pink-900/30 text-gray-700 dark:text-gray-300 hover:text-pink-600 dark:hover:text-pink-400 transition-all duration-300 transform hover:scale-110" title="GitHub">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                    <path fill-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" clip-rule="evenodd"/>
                    </svg>
                            </a>
                            <a href="https://x.com" target="_blank" class="flex items-center justify-center w-12 h-12 rounded-full bg-gray-100 dark:bg-slate-700 hover:bg-pink-100 dark:hover:bg-pink-900/30 text-gray-700 dark:text-gray-300 hover:text-pink-600 dark:hover:text-pink-400 transition-all duration-300 transform hover:scale-110" title="X (Twitter)">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                    </svg>
                            </a>
                        </div>

                        <div class="pt-8 border-t border-gray-200 dark:border-slate-700">
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                <strong class="text-gray-900 dark:text-white">{{__('homepage.email')}}:</strong> contact@poluvtasks.com<br>
                                <strong class="text-gray-900 dark:text-white">{{__('homepage.support')}}:</strong> support@poluvtasks.com
                            </p>
                        </div>
                    </div>

                    {{-- Right: Feedback Form --}}
                    <div>
                        <form id="feedbackForm" class="space-y-6">
                            <div>
                                <label for="feedbackEmail" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                    {{__('homepage.your_email')}}
                                </label>
                                <input 
                                    type="email" 
                                    id="feedbackEmail" 
                                    name="email" 
                                    required
                                    class="w-full px-4 py-3 bg-white dark:bg-slate-700 border border-gray-200 dark:border-slate-600 rounded-xl text-gray-900 dark:text-white focus:border-pink-500 dark:focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20 outline-none transition"
                                    placeholder="your.email@example.com"
                                />
                            </div>
                            <div>
                                <label for="feedbackMessage" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                    {{__('homepage.your_feedback_suggestion')}}
                                </label>
                                <textarea 
                                    id="feedbackMessage" 
                                    name="message" 
                                    rows="6" 
                                    required
                                    class="w-full px-4 py-3 bg-white dark:bg-slate-700 border border-gray-200 dark:border-slate-600 rounded-xl text-gray-900 dark:text-white focus:border-pink-500 dark:focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20 outline-none transition resize-none"
                                    placeholder="{{__('homepage.tell_us_what_you_think_or_share_your_suggestions')}}"
                                ></textarea>
                            </div>
                            <button 
                                type="submit" 
                                class="w-full px-6 py-3 bg-black dark:bg-white text-white dark:text-gray-900 font-semibold rounded-xl hover:bg-gray-800 dark:hover:bg-gray-100 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5"
                            >
                                {{__('homepage.send_feedback')}}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </section>
            </main>

    {{-- Footer --}}
    <x-partials.footer />
        </div>

@push('scripts')
<script>
    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            const target = document.querySelector(targetId);
            if (target) {
                // Calculate offset for sticky header (80px)
                const headerOffset = 80;
                const elementPosition = target.getBoundingClientRect().top;
                const offsetPosition = elementPosition + window.pageYOffset - headerOffset;

                window.scrollTo({
                    top: offsetPosition,
                    behavior: 'smooth'
                });
            }
        });
    });

    // Feedback form submission
    document.getElementById('feedbackForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        const email = document.getElementById('feedbackEmail').value;
        const message = document.getElementById('feedbackMessage').value;
        
        // Here you can add API call to send feedback
        // For now, just show an alert
        alert('{{__('homepage.thank_you_for_your_feedback_we_ll_get_back_to_you_soon')}}');
        this.reset();
    });

    // Theme toggle functionality (if not already in navigation)
    const themeToggle = document.getElementById('themeToggle');
    if (themeToggle) {
        themeToggle.addEventListener('click', () => {
            document.documentElement.classList.toggle('dark');
            const isDark = document.documentElement.classList.contains('dark');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
        });
    }
</script>
@endpush
@endsection
