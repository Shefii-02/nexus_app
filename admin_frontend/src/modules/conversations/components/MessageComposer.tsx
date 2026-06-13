// src/modules/conversations/components/MessageComposer.tsx
import { useState, useRef, useEffect } from 'react';
import type { Message } from '../services/chatService';

interface Props {
  replyTo: Message | null;
  onCancelReply: () => void;
  onSend: (params: { message?: string; type?: string; media?: File; reply_to?: number }) => Promise<void>;
  onTyping: (typing: boolean) => void;
}

export function MessageComposer({ replyTo, onCancelReply, onSend, onTyping }: Props) {
  const [text, setText] = useState('');
  const [showAttach, setShowAttach] = useState(false);
  const [recording, setRecording] = useState(false);
  const [recordingTime, setRecordingTime] = useState(0);
  const [previewFile, setPreviewFile] = useState<{ file: File; type: string; url: string } | null>(null);
  const [sending, setSending] = useState(false);

  const textareaRef = useRef<HTMLTextAreaElement>(null);
  const imageInputRef = useRef<HTMLInputElement>(null);
  const fileInputRef  = useRef<HTMLInputElement>(null);
  const mediaRecorderRef = useRef<MediaRecorder | null>(null);
  const audioChunksRef   = useRef<Blob[]>([]);
  const typingTimerRef   = useRef<ReturnType<typeof setTimeout> | null>(null);
  const recordTimerRef   = useRef<ReturnType<typeof setInterval> | null>(null);

  // Auto-resize textarea
  useEffect(() => {
    const ta = textareaRef.current;
    if (!ta) return;
    ta.style.height = 'auto';
    ta.style.height = Math.min(ta.scrollHeight, 120) + 'px';
  }, [text]);

  const handleTextChange = (e: React.ChangeEvent<HTMLTextAreaElement>) => {
    setText(e.target.value);
    onTyping(true);
    if (typingTimerRef.current) clearTimeout(typingTimerRef.current);
    typingTimerRef.current = setTimeout(() => onTyping(false), 2000);
  };

  const handleSendText = async () => {
    if (!text.trim() && !previewFile) return;
    setSending(true);
    try {
      if (previewFile) {
        await onSend({ type: previewFile.type, media: previewFile.file, message: text || undefined, reply_to: replyTo?.id });
        setPreviewFile(null);
      } else {
        await onSend({ message: text.trim(), type: 'text', reply_to: replyTo?.id });
      }
      setText('');
      onTyping(false);
    } finally {
      setSending(false);
    }
  };

  const handleKeyDown = (e: React.KeyboardEvent) => {
    if (e.key === 'Enter' && !e.shiftKey) {
      e.preventDefault();
      handleSendText();
    }
  };

  const handleFileSelect = (e: React.ChangeEvent<HTMLInputElement>, type: string) => {
    const file = e.target.files?.[0];
    if (!file) return;
    const url = URL.createObjectURL(file);
    setPreviewFile({ file, type, url });
    setShowAttach(false);
    e.target.value = '';
  };

  // Voice recording
  const startRecording = async () => {
    try {
      const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
      const mr = new MediaRecorder(stream);
      mediaRecorderRef.current = mr;
      audioChunksRef.current = [];

      mr.ondataavailable = e => audioChunksRef.current.push(e.data);
      mr.onstop = async () => {
        const blob = new Blob(audioChunksRef.current, { type: 'audio/webm' });
        const file = new File([blob], `voice-${Date.now()}.webm`, { type: 'audio/webm' });
        stream.getTracks().forEach(t => t.stop());
        await onSend({ type: 'voice', media: file, reply_to: replyTo?.id });
      };

      mr.start();
      setRecording(true);
      setRecordingTime(0);
      recordTimerRef.current = setInterval(() => setRecordingTime(t => t + 1), 1000);
    } catch {
      alert('Microphone permission denied.');
    }
  };

  const stopRecording = () => {
    mediaRecorderRef.current?.stop();
    setRecording(false);
    if (recordTimerRef.current) clearInterval(recordTimerRef.current);
    setRecordingTime(0);
  };

  const cancelRecording = () => {
    mediaRecorderRef.current?.stream.getTracks().forEach(t => t.stop());
    setRecording(false);
    if (recordTimerRef.current) clearInterval(recordTimerRef.current);
    setRecordingTime(0);
  };

  const formatRecordTime = (s: number) => `${Math.floor(s / 60)}:${String(s % 60).padStart(2, '0')}`;

  return (
    <div className="composer">
      {/* Reply Preview */}
      {replyTo && (
        <div className="reply-bar">
          <div className="reply-bar-accent" />
          <div className="reply-bar-content">
            <span className="reply-bar-name">{replyTo.sender?.name}</span>
            <span className="reply-bar-text">
              {replyTo.type !== 'text' ? `📎 ${replyTo.type}` : replyTo.message?.substring(0, 80)}
            </span>
          </div>
          <button className="reply-bar-close" onClick={onCancelReply}>✕</button>
        </div>
      )}

      {/* File preview */}
      {previewFile && (
        <div className="file-preview">
          {previewFile.type === 'image' && (
            <img src={previewFile.url} alt="Preview" />
          )}
          {previewFile.type === 'video' && (
            <video src={previewFile.url} controls style={{ maxHeight: 120 }} />
          )}
          {(previewFile.type === 'audio' || previewFile.type === 'file') && (
            <div className="file-preview-name">
              📎 {previewFile.file.name}
            </div>
          )}
          <button className="preview-remove" onClick={() => setPreviewFile(null)}>✕</button>
        </div>
      )}

      {/* Main input row */}
      <div className="composer-row">
        {/* Attach button */}
        <div className="attach-container">
          <button
            className="icon-btn composer-btn"
            onClick={() => setShowAttach(!showAttach)}
            title="Attach"
          >
            <svg width="22" height="22" fill="none" stroke="currentColor" strokeWidth="2" viewBox="0 0 24 24">
              <path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"/>
            </svg>
          </button>

          {showAttach && (
            <div className="attach-menu">
              <button onClick={() => imageInputRef.current?.click()}>
                <span>🖼️</span> Photo/Video
              </button>
              <button onClick={() => fileInputRef.current?.click()}>
                <span>📄</span> Document
              </button>
              <input
                ref={imageInputRef}
                type="file"
                accept="image/*,video/*"
                hidden
                onChange={e => {
                  const file = e.target.files?.[0];
                  if (!file) return;
                  const type = file.type.startsWith('video') ? 'video' : 'image';
                  handleFileSelect(e as any, type);
                }}
              />
              <input
                ref={fileInputRef}
                type="file"
                accept="*/*"
                hidden
                onChange={e => handleFileSelect(e as any, 'file')}
              />
            </div>
          )}
        </div>

        {/* Recording UI */}
        {recording ? (
          <div className="recording-row">
            <button className="icon-btn cancel-rec" onClick={cancelRecording}>✕</button>
            <div className="recording-indicator">
              <span className="rec-dot" />
              <span>{formatRecordTime(recordingTime)}</span>
            </div>
            <button className="send-btn recording" onClick={stopRecording} title="Send voice">
              <svg width="18" height="18" fill="white" viewBox="0 0 24 24">
                <path d="M2 21l21-9L2 3v7l15 2-15 2v7z"/>
              </svg>
            </button>
          </div>
        ) : (
          <>
            {/* Text input */}
            <textarea
              ref={textareaRef}
              className="composer-input"
              placeholder="Type a message..."
              value={text}
              onChange={handleTextChange}
              onKeyDown={handleKeyDown}
              rows={1}
            />

            {/* Send or Mic */}
            {text.trim() || previewFile ? (
              <button
                className="send-btn"
                onClick={handleSendText}
                disabled={sending}
                title="Send"
              >
                {sending ? (
                  <div className="send-spinner" />
                ) : (
                  <svg width="18" height="18" fill="white" viewBox="0 0 24 24">
                    <path d="M2 21l21-9L2 3v7l15 2-15 2v7z"/>
                  </svg>
                )}
              </button>
            ) : (
              <button
                className="send-btn mic"
                onMouseDown={startRecording}
                onTouchStart={startRecording}
                title="Hold to record"
              >
                <svg width="18" height="18" fill="white" viewBox="0 0 24 24">
                  <path d="M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z"/>
                  <path d="M19 10v2a7 7 0 0 1-14 0v-2M12 19v4M8 23h8"/>
                </svg>
              </button>
            )}
          </>
        )}
      </div>
    </div>
  );
}
