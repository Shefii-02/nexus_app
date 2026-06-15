// src/modules/conversations/components/CreateConversationModal.tsx
import { useState, useEffect, useRef } from 'react';
import { searchUsers, getCourses, type User, type Course } from '../services/chatService';
import { getInitials } from '../utils';

interface Props {
  onClose: () => void;
  onCreate: (userId: number, moduleId?: number) => Promise<void>;
}

export function CreateConversationModal({ onClose, onCreate }: Props) {
  const [query, setQuery]         = useState('');
  const [results, setResults]     = useState<User[]>([]);
  const [picked, setPicked]       = useState<User | null>(null);
  const [courses, setCourses]     = useState<Course[]>([]);
  const [courseId, setCourseId]   = useState<number | null>(null);
  const [searching, setSearching] = useState(false);
  const [loading, setLoading]     = useState(false);
  const [error, setError]         = useState('');
  const searchTimer               = useRef<ReturnType<typeof setTimeout>>();

  // Load courses once
  useEffect(() => {
    getCourses().then(setCourses).catch(() => {});
  }, []);

  // Debounced search
  useEffect(() => {
    clearTimeout(searchTimer.current);
    if (picked) return;                           // already selected
    if (query.trim().length < 2) { setResults([]); return; }
    setSearching(true);
    searchTimer.current = setTimeout(async () => {
      try {
        const data = await searchUsers(query.trim());
        setResults(data);
      } catch {
        setResults([]);
      } finally {
        setSearching(false);
      }
    }, 350);
  }, [query, picked]);

  const selectUser = (user: User) => {
    setPicked(user);
    setQuery(user.name);
    setResults([]);
  };

  const clearUser = () => {
    setPicked(null);
    setQuery('');
    setResults([]);
  };

  const handle = async () => {
    if (!picked) { setError('Select a user to chat with.'); return; }
    setLoading(true);
    setError('');
    try {
      await onCreate(picked.id, courseId ?? undefined);
      onClose();
    } catch (e: any) {
      setError(e?.response?.data?.message ?? 'Failed to start chat.');
      setLoading(false);
    }
  };

  return (
    <div className="modal-overlay" onClick={onClose}>
      <div className="modal-box" onClick={e => e.stopPropagation()}>
        {/* Header */}
        <div className="modal-header">
          <button className="icon-btn" onClick={onClose}>
            <svg width="18" height="18" fill="none" stroke="currentColor" strokeWidth="2.5" viewBox="0 0 24 24">
              <path d="M19 12H5M12 5l-7 7 7 7"/>
            </svg>
          </button>
          <h3>New Chat</h3>
        </div>

        {error && <p className="form-error">{error}</p>}

        {/* User search */}
        <div className="form-group">
          <label>Search User</label>
          <div className="search-box" style={{ position: 'relative' }}>
            <svg width="15" height="15" fill="none" stroke="currentColor" strokeWidth="2" viewBox="0 0 24 24">
              <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
            <input
              placeholder="Name, email or mobile…"
              value={query}
              onChange={e => { setQuery(e.target.value); if (picked) clearUser(); }}
              autoFocus
            />
            {searching && <span className="search-spinner" />}
            {(query || picked) && (
              <button className="clear-btn" onClick={clearUser}>✕</button>
            )}
          </div>

          {/* Selected user pill */}
          {picked && (
            <div className="picked-user">
              <div className="conv-avatar sm">
                {picked.avatar
                  ? <img src={picked.avatar} alt={picked.name} />
                  : <span className="avatar-initials">{getInitials(picked.name)}</span>}
              </div>
              <div className="user-search-info">
                <span className="user-search-name">{picked.name}</span>
                {picked.email && <span className="user-search-sub">{picked.email}</span>}
              </div>
              <button className="icon-btn" onClick={clearUser}>✕</button>
            </div>
          )}

          {/* Dropdown */}
          {!picked && results.length > 0 && (
            <div className="user-search-dropdown">
              {results.map(u => (
                <div key={u.id} className="user-search-row" onClick={() => selectUser(u)}>
                  <div className="conv-avatar sm">
                    {u.avatar
                      ? <img src={u.avatar} alt={u.name} />
                      : <span className="avatar-initials">{getInitials(u.name)}</span>}
                  </div>
                  <div className="user-search-info">
                    <span className="user-search-name">{u.name}</span>
                    {u.email  && <span className="user-search-sub">{u.email}</span>}
                    {u.mobile && <span className="user-search-sub">{u.mobile}</span>}
                  </div>
                </div>
              ))}
            </div>
          )}
        </div>

        {/* Course (optional, nullable) */}
        {/* <div className="form-group">
          <label>
            Course / Module
            <span className="label-hint">(optional)</span>
          </label>
          <select
            value={courseId ?? ''}
            onChange={e => setCourseId(e.target.value === '' ? null : Number(e.target.value))}
          >
            <option value="">No course (global chat)</option>
            {courses.map(c => (
              <option key={c.id} value={c.id}>{c.title}</option>
            ))}
          </select>
        </div> */}

        <div className="modal-actions">
          <button onClick={onClose}>Cancel</button>
          <button
            className="primary"
            onClick={handle}
            disabled={loading || !picked}
          >
            {loading ? 'Starting…' : 'Start Chat'}
          </button>
        </div>
      </div>
    </div>
  );
}