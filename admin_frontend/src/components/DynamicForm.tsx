import { useForm } from 'react-hook-form'
import { useEffect } from 'react'
import FloatingInput from './form/FloatingInput'
import SelectField from './form/SelectField'
import TextareaField from './form/TextareaField'
import CheckboxField from './form/Checkbox'
import RadioField from './form/RadioField'
import CheckboxGroup from './form/CheckboxGroup'
import Thumbnail from './form/Thumbnail'
import ImageUpload from './form/ImageUpload'
import Button from './Button'
import DateField from './form/DateField'
import { toast } from 'react-toastify'
import FileField from './form/FileField'
import UserSearchField from './form/UserSearchField'
import UserMultiSearchField from './form/UserMultiSearchField'
import ToggleField from './form/ToggleField'
import AsyncSelectField from './form/AsyncSelectField'

interface FieldConfig {
  name: string
  label: string

  type: string
  required?: boolean
  options?: any[]

  colSpan?: number
  section?: string
  hideOnEdit?: boolean

  endpoint?: string
  valueKey?: string
  labelKey?: string

  /** 🔥 NEW */
  dependsOn?: string
  dependsValue?: any
  readOnly?: boolean
}

interface Props {
  config: FieldConfig[]
  defaultValues?: any
  onSubmit: (data: any) => Promise<any>
  isEdit?: boolean
}

const DynamicForm = ({ config, defaultValues = {}, onSubmit, isEdit }: Props) => {
  /** =========================
   * 🔥 Extract defaults from config
   ========================= */
  const getDefaultsFromConfig = (config: FieldConfig[]) => {
    const defaults: Record<string, any> = {}

    config.forEach((field) => {
      // RADIO DEFAULT
      if (field.type === 'radio' && field.options) {
        const def = field.options.find((o: any) => o.default)
        if (def) defaults[field.name] = def.value
      }

      // CHECKBOX GROUP DEFAULT
      if (field.type === 'checkbox-group' && field.options) {
        defaults[field.name] = field.options
          .filter((o: any) => o.default)
          .map((o: any) => o.value)
      }
    })

    return defaults
  }

  const configDefaults = getDefaultsFromConfig(config)

  /** =========================
   * FORM INIT
   ========================= */
  const {
    register,
    handleSubmit,
    setError,
    setValue,
    reset,
    watch,
    formState: { errors },
  } = useForm({
    defaultValues: {
      ...configDefaults,   // 🔥 config default
      ...defaultValues,    // 🔥 API override
    },
  })

  useEffect(() => {
    if (defaultValues && Object.keys(defaultValues).length > 0) {
      reset(defaultValues)
    }
  }, [defaultValues, reset])

  // useEffect(() => {
  //   console.log('RESET VALUES', defaultValues)
  // }, [defaultValues])


  /** =========================
   * GROUP BY SECTION
   ========================= */
  const sections: Record<string, FieldConfig[]> = {}

  config.forEach((field) => {
    if (field.hideOnEdit && isEdit) return

    const section = field.section || 'default'
    if (!sections[section]) sections[section] = []
    sections[section].push(field)
  })

  /** =========================
   * RENDER
   ========================= */
  return (
    <form
      onSubmit={handleSubmit(async (data) => {
        try {
          await onSubmit(data)
        } catch (err: any) {
          // const response = err?.response?.data
          // const message = response?.message

          // const sqlError = response?.data?.error
          // 1. show main message
          // toast.error(message || 'Something went wrong')

          // 2. show exact backend error
          // if (sqlError) {
          //   toast.error(sqlError)
          // }

          const apiErrors = err?.response?.data?.errors

          if (apiErrors) {
            Object.entries(apiErrors).forEach(([field, messages]) => {
              const msg = (messages as string[])[0]

              // field error in UI
              setError(field as any, {
                type: 'server',
                message: msg,
              })

              // toast error
              toast.error(`${field}: ${msg}`)
            })
          }
        }
      })}
      className="space-y-6"
    >
      {Object.entries(sections).map(([sectionName, fields]) => (
        <div key={sectionName} className="rounded-2xl border p-5 bg-white shadow-sm">
          {sectionName !== 'default' && (
            <h3 className="text-lg font-semibold mb-4 capitalize">
              {sectionName.replace('_', ' ')}
            </h3>
          )}

          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            {fields.map((field) => {
              const colSpan = field.colSpan === 2 ? 'md:col-span-2' : ''

              /** =========================
               * 🔥 DEPENDENCY CHECK
               ========================= */
              if (field.dependsOn) {
                const watched = watch(field.dependsOn)

                if (watched !== field.dependsValue) {
                  return null
                }
              }

              return (
                <div key={field.name} className={colSpan}>
                  {(() => {
                    switch (field.type) {
                      case 'select':
                        return (
                          <SelectField
                            label={field.label}
                            name={field.name}
                            register={register}
                            options={field.options || []}
                            error={errors[field.name]}
                            disabled={field.readOnly}
                          />
                        )

                      case 'textarea':
                        return (
                          <TextareaField
                            label={field.label}
                            name={field.name}
                            register={register}
                            error={errors[field.name]}
                            readOnly={field.readOnly}
                          />
                        )

                      case 'checkbox':
                        return (
                          <CheckboxField
                            label={field.label}
                            name={field.name}
                            register={register}
                            disabled={field.readOnly}
                          />
                        )

                      case 'radio':
                        return (
                          <RadioField
                            label={field.label}
                            name={field.name}
                            options={field.options || []}
                            register={register}
                            watch={watch}   // ✅ important
                            error={errors[field.name]}
                            disabled={field.readOnly}
                          />
                        )

                      case 'checkbox-group':
                        return (
                          <CheckboxGroup
                            label={field.label}
                            name={field.name}
                            options={field.options || []}
                            watch={watch}
                            setValue={setValue}
                            error={errors[field.name]}
                            disabled={field.readOnly}
                          />
                        )
                      case 'file':
                        return (
                          <FileField
                            label={field.label}
                            name={field.name}
                            setValue={setValue}
                            register={register}
                            watch={watch}
                            error={errors[field.name]}
                            disabled={field.readOnly}
                          />
                        )

                      case 'thumbnail':
                        return (
                          <Thumbnail
                            label={field.label}
                            name={field.name}
                            setValue={setValue}
                            register={register}
                            watch={watch}
                            error={errors[field.name]}
                            disabled={field.readOnly}
                          />
                        )

                      case 'date':
                        return (
                          <DateField
                            label={field.label}
                            name={field.name}
                            register={register}
                            watch={watch}
                            error={errors[field.name]}
                          />
                        )
                      case 'image-with-crop':
                        return (
                          <ImageUpload
                            label={field.label}
                            name={field.name}
                            setValue={setValue}
                            watch={watch}
                            error={errors[field.name]}
                            disabled={field.readOnly}
                          />
                        )

                      case 'user-search':
                        return (
                          <UserSearchField
                            label={field.label}
                            onChange={(id) =>
                              setValue(
                                field.name,
                                id
                              )
                            }
                          />
                        )

                      case 'user-multi-search':
                        return (
                          <UserMultiSearchField
                            label={field.label}
                            onChange={(ids) =>
                              setValue(
                                field.name,
                                ids
                              )
                            }
                          />
                        )
                      case 'toggle':
                        return (
                          <ToggleField
                            label={field.label}
                            name={field.name}
                            register={register}
                            disabled={field.readOnly}
                          />
                        )
                      case 'async-select':
                        return (
                          <AsyncSelectField
                            label={field.label}
                            endpoint={
                              field.endpoint!
                            }
                            value={watch(
                              field.name
                            )}
                            valueKey={
                              field.valueKey
                            }
                            labelKey={
                              field.labelKey
                            }
                            onChange={(value) =>
                              setValue(
                                field.name,
                                value
                              )
                            }
                          />
                        )

                      default:
                        return (
                          <FloatingInput
                            label={field.label}
                            name={field.name}
                            register={register}
                            rules={{
                              required:
                                field.required &&
                                `${field.label} is required`,
                            }}
                            type={field.type}
                            error={errors[field.name]}
                            readOnly={field.readOnly}
                          />
                        )
                    }
                  })()}
                </div>
              )
            })}
          </div>
        </div>
      ))}

      <Button type="submit" className="w-full md:w-auto">
        Submit
      </Button>
    </form>
  )
}

export default DynamicForm