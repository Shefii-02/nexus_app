import ReactPlayer from 'react-player'

interface Props {
  url: string
}

const YoutubePlayer = ({ url }: Props) => {
  return (
    <div className="aspect-video w-full rounded-xl overflow-hidden">
      <ReactPlayer
        url={url}
        width="100%"
        height="100%"
        controls
      />
    </div>
  )
}

export default YoutubePlayer