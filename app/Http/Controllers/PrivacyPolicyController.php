<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class PrivacyPolicyController extends Controller
{
    /**
     * Display the privacy policy page.
     */
    public function __invoke(): Response
    {
        return Inertia::render('PrivacyPolicy', [
            'translations' => [
                'title' => __('privacy.title'),
                'intro' => __('privacy.intro'),
                'last_updated' => __('privacy.last_updated'),
                'summary_title' => __('privacy.summary_title'),
                'data_collected_title' => __('privacy.data_collected_title'),
                'data_collected' => trans('privacy.data_collected'),
                'data_use_title' => __('privacy.data_use_title'),
                'data_use' => trans('privacy.data_use'),
                'play_requirements_title' => __('privacy.play_requirements_title'),
                'play_requirements' => trans('privacy.play_requirements'),
                'sharing_title' => __('privacy.sharing_title'),
                'sharing' => trans('privacy.sharing'),
                'rights_title' => __('privacy.rights_title'),
                'rights' => trans('privacy.rights'),
                'retention_title' => __('privacy.retention_title'),
                'retention' => trans('privacy.retention'),
                'security_title' => __('privacy.security_title'),
                'security' => trans('privacy.security'),
                'contact_title' => __('privacy.contact_title'),
                'contact_description' => __('privacy.contact_description'),
                'contact_email_label' => __('privacy.contact_email_label'),
                'contact_email' => __('privacy.contact_email'),
                'contact_phone_label' => __('privacy.contact_phone_label'),
                'contact_phone' => __('privacy.contact_phone'),
            ],
        ]);
    }
}
