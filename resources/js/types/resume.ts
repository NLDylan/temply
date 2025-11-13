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

export interface ResumeEducation {
    id: string
    resume_id: string
    institution: string
    degree: string | null
    field_of_study: string | null
    location: string | null
    started_on: string | null
    ended_on: string | null
    is_current: boolean
    description: string | null
    sort_order: number
}

export interface ResumeExperience {
    id: string
    resume_id: string
    company: string
    role: string
    employment_type: string | null
    location: string | null
    started_on: string | null
    ended_on: string | null
    is_current: boolean
    description: string | null
    sort_order: number
}

export interface ResumeSkill {
    id: string
    resume_id: string
    name: string
    category: string | null
    proficiency: string | null
    is_featured: boolean
    sort_order: number
    metadata: Record<string, any> | null
}

export interface ResumeLanguage {
    id: string
    resume_id: string
    language: string
    proficiency: string | null
    is_native: boolean
    sort_order: number
    metadata: Record<string, any> | null
}

export interface ResumeCertification {
    id: string
    resume_id: string
    name: string
    issuer: string | null
    issued_on: string | null
    expires_on: string | null
    credential_id: string | null
    credential_url: string | null
    description: string | null
    sort_order: number
    metadata: Record<string, any> | null
}

export interface ResumeProject {
    id: string
    resume_id: string
    name: string
    role: string | null
    organization: string | null
    url: string | null
    started_on: string | null
    ended_on: string | null
    is_current: boolean
    description: string | null
    sort_order: number
    metadata: Record<string, any> | null
}

export interface ResumeVolunteering {
    id: string
    resume_id: string
    organization: string
    role: string | null
    location: string | null
    started_on: string | null
    ended_on: string | null
    is_current: boolean
    description: string | null
    sort_order: number
    metadata: Record<string, any> | null
}

export interface ResumeAchievement {
    id: string
    resume_id: string
    title: string
    issuer: string | null
    achieved_on: string | null
    category: string | null
    url: string | null
    description: string | null
    sort_order: number
    metadata: Record<string, any> | null
}

export interface ResumeResource {
    id: string
    user_id: string
    template_id: string | null
    title: string | null
    slug: string | null
    headline: string | null
    location: string | null
    summary: string | null
    profile: ResumeProfile
    settings: Record<string, any> | null
    expires_at: string | null
    locked_at: string | null
    created_at: string | null
    updated_at: string | null
    education?: ResumeEducation[]
    experience?: ResumeExperience[]
    skills?: ResumeSkill[]
    languages?: ResumeLanguage[]
    certifications?: ResumeCertification[]
    projects?: ResumeProject[]
    volunteering?: ResumeVolunteering[]
    achievements?: ResumeAchievement[]
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


