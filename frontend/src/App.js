import React from 'react';
import Upload from './components/Upload';
import Chat from './components/Chat';

function App() {
  return (
    <div style={styles.page}>
      <div style={styles.container}>
        <h1>Document Q&A (RAG)</h1>
        <p>Upload a PDF and ask questions from your document.</p>
        <Upload />
        <Chat />
      </div>
    </div>
  );
}

const styles = {
  page: {
    minHeight: '100vh',
    display: 'flex',
    justifyContent: 'center',
    alignItems: 'center',
    background: '#f0f2f5',
    padding: 16,
  },
  container: {
    width: '100%',
    maxWidth: 800,
    background: '#fff',
    borderRadius: 10,
    padding: 20,
    boxShadow: '0 3px 10px rgba(0,0,0,0.08)',
  },
};

export default App;
