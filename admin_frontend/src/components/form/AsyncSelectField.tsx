import { useEffect, useRef, useState } from 'react'
import apiClient from '../../services/apiClient'

interface Props {
  label: string
  endpoint: string
  value?: any
  valueKey?: string
  labelKey?: string
  placeholder?: string
  onChange: (id: any) => void
}

const AsyncSelectField = ({
  label,
  endpoint,
  value,
  valueKey = 'id',
  labelKey = 'name',
  placeholder,
  onChange,
}: Props) => {
  const [search, setSearch] = useState('')
  const [items, setItems] = useState<any[]>([])
  const [loading, setLoading] = useState(false)
  const [open, setOpen] = useState(false)
  const [selected, setSelected] = useState<any>(null)

  // Track the last id we synced so we don't re-fire when the parent
  // re-renders with the same value after we already called onChange.
  const syncedIdRef = useRef<any>(undefined)

  useEffect(() => {
    if (value == null) return

    if (typeof value === 'object') {
      const id = value[valueKey]

      // Already synced this id — nothing to do
      if (syncedIdRef.current === id) return

      syncedIdRef.current = id
      setSelected(value)

      // Tell the form to store the id, not the full object
      onChange(id)
    } else {
      // value is already a plain id — just make sure the label is shown
      if (syncedIdRef.current === value) return
      syncedIdRef.current = value

      // selected is already set (user picked from dropdown) — skip fetch
      if (selected && selected[valueKey] === value) return

      apiClient
        .get(endpoint, { params: { search: value } })
        .then((res) => {
          const list: any[] = res.data.data || res.data || []
          const match = list.find((item) => item[valueKey] === value)
          if (match) setSelected(match)
        })
        .catch(() => {})
    }
  }, [value])

  useEffect(() => {
    if (!search) {
      setItems([])
      return
    }

    const timeout = setTimeout(async () => {
      try {
        setLoading(true)
        const res = await apiClient.get(endpoint, { params: { search } })
        setItems(res.data.data || res.data || [])
      } finally {
        setLoading(false)
      }
    }, 300)

    return () => clearTimeout(timeout)
  }, [search, endpoint])

  return (
    <div className="relative">
      <label className="block text-sm font-medium mb-1">{label}</label>

      <input
        value={selected ? selected[labelKey] : search}
        placeholder={placeholder || `Search ${label}`}
        onChange={(e) => {
          setSelected(null)
          setSearch(e.target.value)
          setOpen(true)
        }}
        onFocus={() => setOpen(true)}
        className="w-full border rounded-lg p-2"
      />

      {open && (
        <div className="absolute z-50 w-full bg-white border rounded-lg shadow-lg mt-1 max-h-64 overflow-auto">
          {loading && (
            <div className="p-3 text-sm text-gray-500">Loading...</div>
          )}

          {!loading && items.length === 0 && search && (
            <div className="p-3 text-sm text-gray-500">No results found</div>
          )}

          {items.map((item) => (
            <div
              key={item[valueKey]}
              className="p-3 hover:bg-gray-100 cursor-pointer border-b"
              onClick={() => {
                const id = item[valueKey]
                syncedIdRef.current = id
                setSelected(item)
                setSearch('')
                setOpen(false)
                onChange(id)
              }}
            >
              <div className="font-medium">{item[labelKey]}</div>
              {item.phone && (
                <div className="text-xs text-gray-500">{item.phone}</div>
              )}
              {item.email && (
                <div className="text-xs text-gray-500">{item.email}</div>
              )}
            </div>
          ))}
        </div>
      )}
    </div>
  )
}

export default AsyncSelectField