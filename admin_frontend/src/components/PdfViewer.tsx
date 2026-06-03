import { Worker, Viewer } from '@react-pdf-viewer/core'
import '@react-pdf-viewer/core/lib/styles/index.css'

interface Props {
  url: string
}

const PdfViewer = ({ url }: Props) => {
  return (
    <div className="h-[600px] border rounded-xl overflow-hidden">
      <Worker workerUrl="https://unpkg.com/pdfjs-dist@3.11.174/build/pdf.worker.min.js">
        <Viewer fileUrl={url} />
      </Worker>
    </div>
  )
}

export default PdfViewer