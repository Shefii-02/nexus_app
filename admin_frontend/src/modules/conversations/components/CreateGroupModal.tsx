// src/modules/conversations/components/CreateGroupModal.tsx
import { useState, useEffect, useRef } from 'react';
import { searchUsers, getCourses, type User, type Course } from '../services/chatService';
import { getInitials } from '../utils';

interface Props {
  onClose: () => void;
  onCreate: (title: string, userIds: number[], moduleId?: number) => Promise<void>;
}

export function CreateGroupModal({ onClose, onCreate }: Props) {
  const [title, setTitle]             = useState('');
  const [query, setQuery]             = useState('');
  const [results, setResults]         = useState<User[]>([]);
  const [selected, setSelected]       = useState<User[]>([]);
  const [courses, setCourses]         = useState<Course[]>([]);
  const [courseId, setCourseId]       = useState<number | null>(null);
  const [searching, setSearching]     = useState(false);
  const [loading, setLoading]         = useState(false);
  const [error, setError]             = useState('');
  const searchTimer                   = useRef<ReturnType<typeof setTimeout>>();

  // Load courses once
  useEffect(() => {
    getCourses().then(setCourses).catch(() => {});
  }, []);

  // Debounced user search
  useEffect(() => {
    clearTimeout(searchTimer.current);
    if (query.trim().length < 2) { setResults([]); return; }
    setSearching(true);
    searchTimer.current = setTimeout(async () => {
      try {
        const data = await searchUsers(query.trim());
        setResults(data.filter(u => !selected.some(s => s.id === u.id)));
      } catch {
        setResults([]);
      } finally {
        setSearching(false);
      }
    }, 350);
  }, [query, selected]);

  const addUser = (user: User) => {
    setSelected(prev => prev.some(u => u.id === user.id) ? prev : [...prev, user]);
    setQuery('');
    setResults([]);
  };

  const removeUser = (id: number) =>
    setSelected(prev => prev.filter(u => u.id !== id));

  const handle = async () => {
    if (!title.trim()) { setError('Group name is required.'); return; }
    if (selected.length < 2) { setError('Add at least 2 participants.'); return; }
    setLoading(true);
    setError('');
    try {
      await onCreate(
        title.trim(),
        selected.map(u => u.id),
        courseId ?? undefined,
      );
      onClose();
    } catch (e: any) {
      setError(e?.response?.data?.message ?? 'Failed to create group.');
      setLoading(false);
    }
  };

  return (
    <div className="modal-overlay" onClick={onClose}>
      <div className="modal-box create-group-modal" onClick={e => e.stopPropagation()}>
        {/* Header */}
        <div className="modal-header">
          <button className="icon-btn" onClick={onClose}>
            <svg width="18" height="18" fill="none" stroke="currentColor" strokeWidth="2.5" viewBox="0 0 24 24">
              <path d="M19 12H5M12 5l-7 7 7 7"/>
            </svg>
          </button>
          <h3>New Group</h3>
        </div>

        {error && <p className="form-error">{error}</p>}

        {/* Group name */}
        <div className="form-group">
          <label>Group Name</label>
          <input
            value={title}
            onChange={e => setTitle(e.target.value)}
            placeholder="e.g. Physics Class 2024"
            autoFocus
          />
        </div>

        {/* Participant search */}
        <div className="form-group">
          <label>Add Participants</label>

          {/* Selected chips */}
          {selected.length > 0 && (
            <div className="participant-chips">
              {selected.map(u => (
                <span key={u.id} className="participant-chip">
                  {u.avatar
                    ? <img src={u.avatar} alt={u.name} />
                    : <span className="chip-initials">{getInitials(u.name)}</span>}
                  {u.name}
                  <button className="chip-remove" onClick={() => removeUser(u.id)}>✕</button>
                </span>
              ))}
            </div>
          )}

          {/* Search input */}
          <div className="search-box">
            <svg width="15" height="15" fill="none" stroke="currentColor" strokeWidth="2" viewBox="0 0 24 24">
              <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
            <input
              placeholder="Search by name, email or mobile…"
              value={query}
              onChange={e => setQuery(e.target.value)}
            />
            {searching && <span className="search-spinner" />}
            {query && <button className="clear-btn" onClick={() => { setQuery(''); setResults([]); }}>✕</button>}
          </div>

          {/* Dropdown results */}
          {results.length > 0 && (
            <div className="user-search-dropdown">
              {results.map(u => (
                <div
                  key={u.id}
                  className="user-search-row"
                  onClick={() => addUser(u)}
                >
                  <div className="conv-avatar sm">
                    {u.avatar
                      ? <img src={u.avatar} alt={u.name} />
                      : <span className="avatar-initials">{getInitials(u.name)}</span>}
                  </div>
                  <div className="user-search-info">
                    <span className="user-search-name">{u.name}</span>
                    {u.email && <span className="user-search-sub">{u.email}</span>}
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
            <span className="label-hint">(optional — isolates chat per course)</span>
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

        {/* Footer */}
        <div className="modal-actions">
          <button onClick={onClose}>Cancel</button>
          <button
            className="primary"
            onClick={handle}
            disabled={loading || !title.trim() || selected.length < 2}
          >
            {loading ? 'Creating…' : 'Create Group'}
          </button>
        </div>
      </div>
    </div>
  );
}