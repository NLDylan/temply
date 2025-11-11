export interface ResumeSectionMeta {
  id: string
  label: string
  status: 'draft' | 'complete' | 'attention'
  hasData: boolean
}

export interface ResumeBuilderState {
  activeSection: string
  completedSections: string[]
  pinnedSections: string[]
  lastSavedAt: string
}

export interface ResumeLayoutPreset {
  id: string
  label: string
  description: string
  thumbnail: string
}

export const resumeBuilderFollowUps = [
  {
    id: 'integration-autosave',
    summary: 'Wire up autosave with Inertia forms and queued PDF generation.',
    effort: 'medium',
  },
  {
    id: 'preview-renderer',
    summary: 'Connect live resume preview to template renderer using Spatie Media Library assets.',
    effort: 'high',
  },
  {
    id: 'section-permissions',
    summary: 'Respect subscription window before allowing edits; fallback to download-only mode.',
    effort: 'medium',
  },
] as const

