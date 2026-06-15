import { useEffect, useState } from 'react'
import apiClient from '../../services/apiClient'

interface Props {
  label: string
  endpoint: string

  value?: any

  valueKey?: string
  labelKey?: string

  placeholder?: string

  onChange: (item: any) => void
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
  const [search, setSearch] =
    useState('')

  const [items, setItems] =
    useState<any[]>([])

  const [loading, setLoading] =
    useState(false)

  const [open, setOpen] =
    useState(false)

  const [selected, setSelected] =
    useState<any>(value)

  useEffect(() => {
    if (!search) {
      setItems([])
      return
    }

    const timeout =
      setTimeout(async () => {
        try {
          setLoading(true)

          const res =
            await apiClient.get(
              endpoint,
              {
                params: {
                  search,
                },
              }
            )

          setItems(
            res.data.data ||
              res.data ||
              []
          )
        } finally {
          setLoading(false)
        }
      }, 300)

    return () =>
      clearTimeout(timeout)
  }, [search])

  return (
    <div className="relative">
      <label className="block text-sm font-medium mb-1">
        {label}
      </label>

      <input
        value={
          selected
            ? selected[labelKey]
            : search
        }
        placeholder={
          placeholder ||
          `Search ${label}`
        }
        onChange={(e) => {
          setSelected(null)
          setSearch(
            e.target.value
          )
          setOpen(true)
        }}
        onFocus={() =>
          setOpen(true)
        }
        className="
          w-full
          border
          rounded-lg
          p-2
        "
      />

      {open && (
        <div
          className="
          absolute
          z-50
          w-full
          bg-white
          border
          rounded-lg
          shadow-lg
          mt-1
          max-h-64
          overflow-auto
        "
        >
          {loading && (
            <div className="p-3 text-sm text-gray-500">
              Loading...
            </div>
          )}

          {!loading &&
            items.length === 0 &&
            search && (
              <div className="p-3 text-sm text-gray-500">
                No results found
              </div>
            )}

          {items.map((item) => (
            <div
              key={
                item[valueKey]
              }
              className="
                p-3
                hover:bg-gray-100
                cursor-pointer
                border-b
              "
              onClick={() => {
                setSelected(item)

                setSearch('')

                setOpen(false)

                onChange(item.id)
              }}
            >
              <div className="font-medium">
                {item[labelKey]}
              </div>

              {item.phone && (
                <div className="text-xs text-gray-500">
                  {item.phone}
                </div>
              )}

              {item.email && (
                <div className="text-xs text-gray-500">
                  {item.email}
                </div>
              )}
            </div>
          ))}
        </div>
      )}
    </div>
  )
}

export default AsyncSelectField