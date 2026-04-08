import React, { useState } from 'react';
import { askQuestion } from '../api';

function Chat() {
  const [question, setQuestion] = useState('');
  const [answer, setAnswer] = useState('');
  const [loading, setLoading] = useState(false);

  const handleAsk = async () => {
    if (!question.trim()) {
      setAnswer('Please type your question.');
      return;
    }

    try {
      setLoading(true);
      setAnswer('Thinking...');
      const data = await askQuestion(question);
      setAnswer(data.answer || 'No answer found.');
    } catch (error) {
      setAnswer(error.message);
    } finally {
      setLoading(false);
    }
  };

  return (
    <div style={styles.box}>
      <h2>Ask Question</h2>
      <input
        style={styles.input}
        type="text"
        value={question}
        onChange={(e) => setQuestion(e.target.value)}
        placeholder="Ask something from your uploaded PDF"
      />
      <button style={styles.btn} onClick={handleAsk} disabled={loading}>
        {loading ? 'Please wait...' : 'Ask'}
      </button>
      {answer && (
        <div style={styles.answerBox}>
          <strong>Answer:</strong>
          <p>{answer}</p>
        </div>
      )}
    </div>
  );
}

const styles = {
  box: { border: '1px solid #ddd', borderRadius: 8, padding: 16, background: '#fff' },
  input: { width: '70%', padding: 8 },
  btn: { marginLeft: 10, padding: '8px 14px', cursor: 'pointer' },
  answerBox: { marginTop: 12, background: '#f7f7f7', padding: 10, borderRadius: 6 },
};

export default Chat;
