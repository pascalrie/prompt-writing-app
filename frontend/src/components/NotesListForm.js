import React, {useState, useEffect} from "react";
import axios from "axios";
import {useNavigate} from "react-router-dom";

const FetchNotes = ({onFetch}) => {
    useEffect(() => {
        const fetchNotes = async () => {
            try {
                const response = await axios.get("http://localhost:8083/api/note/list");
                const notesArray = Object.values(response.data).filter(
                    (item) => item?.id && item?.title && item?.content
                );
                onFetch(notesArray);
            } catch (error) {
                console.error("Error fetching notes:", error);
            }
        };
        fetchNotes();
    }, [onFetch]);

    return null;
};

const NotesListForm = () => {
    const [notes, setNotes] = useState([]);
    const [expandedNote, setExpandedNote] = useState(null);
        const navigate = useNavigate();

    const toggleNoteExpansion = (noteId) => {
        setExpandedNote((prev) => (prev === noteId ? null : noteId));
    };

    return (
        <div>
            <h1>Notes</h1>
            <button onClick={() => navigate("/create/note")}>Create Note</button>
            <FetchNotes onFetch={setNotes}/>
            <div style={styles.gridContainer}>
                {notes.length > 0 ? (
                    notes.map((note) => (
                        <div
                            key={note.id}
                            style={styles.noteCard}
                            onClick={() => toggleNoteExpansion(note.id)}
                        >
                            <h2 style={styles.noteTitle}>{note.title}</h2>
                            <p style={styles.noteContent}>
                                {/* Show full content if expanded, else show preview */}
                                {expandedNote === note.id
                                    ? note.content
                                    : `${note.content.slice(0, 50)}...`}
                            </p>
                            {expandedNote !== note.id && (
                                <button style={styles.expandButton}>+</button>
                            )}
                        </div>
                    ))
                ) : (
                    <p>Loading notes...</p>
                )}
            </div>
        </div>
    );
};

const styles = {
    button: {
        backgroundColor: "#007bff",
        color: "#fff",
        border: "none",
        borderRadius: "5px",
        cursor: "pointer",
        padding: "10px 20px",
        fontSize: "16px",
    },
    buttonHover: {
        backgroundColor: "#0056b3",
    },
    expandButton: {
        backgroundColor: "#007bff",
        color: "#fff",
        border: "none",
        borderRadius: "50%",
        width: "30px",
        height: "30px",
        cursor: "pointer",
        display: "flex",
        alignItems: "center",
        justifyContent: "center",
        fontWeight: "bold",
        fontSize: "1rem",
        boxShadow: "0px 2px 4px rgba(0, 0, 0, 0.2)",
    },
    gridContainer: {
        display: "grid",
        gridTemplateColumns: "repeat(auto-fill, minmax(250px, 1fr))",
        gap: "16px",
        padding: "16px",
    },
    noteCard: {
        backgroundColor: "#f9f9f9",
        border: "1px solid #ddd",
        borderRadius: "8px",
        padding: "16px",
        cursor: "pointer",
        transition: "box-shadow 0.2s",
        boxShadow: "0 4px 6px rgba(0, 0, 0, 0.1)",
    },
    noteCardHover: {
        boxShadow: "0 8px 12px rgba(0, 0, 0, 0.2)",
    },
    noteTitle: {
        fontSize: "1.25rem",
        margin: "0 0 8px 0",
    },
    noteContent: {
        fontSize: "0.875rem",
        color: "#555",
        overflowWrap: "break-word",
        wordWrap: "break-word",
        whiteSpace: "pre-wrap",
        overflow: "hidden",
        textOverflow: "ellipsis",
        maxHeight: "100em",
        lineHeight: "1.5em",
    },

};

export default NotesListForm;