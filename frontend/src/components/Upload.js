import React, { useState } from 'react';
import { uploadFile } from '../api';

function Upload() {
  const [file, setFile] = useState(null);
  const [message, setMessage] = useState('');
  const [loading, setLoading] = useState(false);

  const handleUpload = async () => {
    if (!file) {
      setMessage('Please select a PDF file.');
      return;
    }

    try {
      setLoading(true);
      setMessage('Uploading and indexing...');
      const data = await uploadFile(file);
      setMessage(data.message || 'Upload success');
    } catch (error) {
      setMessage(error.message);
    } finally {
      setLoading(false);
    }
  };

  return (
    <div style={styles.box}>
      <h2>Upload PDF</h2>
      <input
        type="file"
        accept="application/pdf"
        onChange={(e) => setFile(e.target.files[0])}
      />
      <button style={styles.btn} onClick={handleUpload} disabled={loading}>
        {loading ? 'Please wait...' : 'Upload'}
      </button>
      {message && <p>{message}</p>}
    </div>
  );
}

const styles = {
  box: { border: '1px solid #ddd', borderRadius: 8, padding: 16, marginBottom: 16, background: '#fff' },
  btn: { marginLeft: 10, padding: '8px 14px', cursor: 'pointer' },
};

export default Upload;
