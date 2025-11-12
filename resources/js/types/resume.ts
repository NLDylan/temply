export interface ResumeContactLink {
    id: string
    label: string
    url: string
}

export interface ResumeProfile {
    full_name: string
    email: string | null
    phone: string | null
    working_rights: string | null
    contact_links: ResumeContactLink[]
}

export interface ResumeResource {
    id: string
    title: string | null
    headline: string | null
    location: string | null
    summary?: string | null
    profile: ResumeProfile
    updated_at: string | null
}

export interface ResumeBasicInformationFormData {
    headline: string
    location: string
    profile: {
        full_name: string
        email: string
        phone: string
        working_rights: string
        contact_links: Array<{
            id: string
            label: string
            url: string
        }>
    }
}


