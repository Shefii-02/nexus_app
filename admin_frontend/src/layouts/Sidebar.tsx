import { useState } from 'react'
import { Link, useLocation } from 'react-router-dom'

type NavChild = {
  label: string
  path: string
}

type NavItem =
  | { label: string; path: string }
  | { label: string; children: NavChild[] }

type NavSection = {
  title: string
  items: NavItem[]
}

const isGroup = (item: NavItem): item is { label: string; children: NavChild[] } =>
  'children' in item

interface Props {
  sections: NavSection[]
  user: any
  onNavigate?: () => void
}

const Sidebar = ({ sections, user, onNavigate }: Props) => {
  const location = useLocation()
  const [openGroup, setOpenGroup] = useState<string | null>(null)

  const isActive = (path: string) => location.pathname === path

  return (
    <nav className="space-y-6">
      {sections.map((section) => (
        <div key={section.title}>
          <p className="mb-3 text-xs uppercase tracking-[0.24em] text-slate-400">
            {section.title}
          </p>

          <div className="space-y-1">
            {section.items
              .filter((item) => item.label !== 'Staff' || user?.acc_type === 'admin')
              .map((item) =>
                isGroup(item) ? (
                  <div key={item.label} className="border-b pb-2">
                    <button
                      onClick={() =>
                        setOpenGroup(openGroup === item.label ? null : item.label)
                      }
                      className="w-full text-left px-3 py-2 text-sm font-medium text-slate-800"
                    >
                      {item.label}
                    </button>

                    {openGroup === item.label && (
                      <div className="ml-2 space-y-1">
                        {item.children.map((sub) => (
                          <Link
                            key={sub.path}
                            to={sub.path}
                            onClick={onNavigate}
                            className={`block px-3 py-2 rounded-lg text-sm ${
                              isActive(sub.path)
                                ? 'bg-black text-white'
                                : 'text-slate-700 hover:bg-slate-100'
                            }`}
                          >
                            {sub.label}
                          </Link>
                        ))}
                      </div>
                    )}
                  </div>
                ) : (
                  <Link
                    key={item.path}
                    to={item.path}
                    onClick={onNavigate}
                    className={`block px-4 py-2 rounded-lg text-sm ${
                      isActive(item.path)
                        ? 'bg-black text-white'
                        : 'text-slate-700 hover:bg-slate-100'
                    }`}
                  >
                    {item.label}
                  </Link>
                )
              )}
          </div>
        </div>
      ))}
    </nav>
  )
}

export default Sidebar