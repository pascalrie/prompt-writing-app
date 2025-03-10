import React, {useState, useEffect} from "react";
import axios from "axios";

const FetchFoldersWithNotes = ({onFetch}) => {
    useEffect(() => {
        const fetchFoldersWithNotes = async () => {
            try {
                const response = await axios.get("http://localhost:8083/api/folder/list");
                const foldersArray = Object.values(response.data).filter(
                    (item) => item?.id && item?.title && item?.notes // Ensures proper structure
                );
                onFetch(foldersArray);
            } catch (error) {
                console.error("Error fetching folders with notes:", error);
            }
        };

        fetchFoldersWithNotes();
    }, [onFetch]);

    return null;
};

const FoldersListForm = () => {
    const [folders, setFolders] = useState([]);
    const [expandedFolderId, setExpandedFolderId] = useState(null);
    const [expandedNotes, setExpandedNotes] = useState({}); // Tracks expanded state for each note

    const toggleFolderExpansion = (folderId) => {
        setExpandedFolderId((prev) => (prev === folderId ? null : folderId));
    };

    const toggleNoteExpansion = (noteId) => {
        setExpandedNotes((prev) => ({
            ...prev,
            [noteId]: !prev[noteId], // Toggle the expanded state of the specific note
        }));
    };

    return (
        <div>
            <h1>Folders</h1>
            <FetchFoldersWithNotes onFetch={setFolders}/>

            <div style={styles.folderListContainer}>
                {folders.length > 0 ? (
                    folders.map((folder) => (
                        <div key={folder.id} style={styles.folderItem}>
                            <div
                                style={styles.folderHeader}
                                onClick={() => toggleFolderExpansion(folder.id)}
                            >
                                <h2 style={styles.folderTitle}>{folder.title}</h2>
                                <button style={styles.expandButton}>
                                    {expandedFolderId === folder.id ? "−" : "+"}
                                </button>
                            </div>

                            {expandedFolderId === folder.id && (
                                <div style={styles.notesGrid}>
                                    {folder.notes.length > 0 ? (
                                        folder.notes.map((note) => (
                                            <div key={note.id} style={styles.noteCard}>
                                                <h3 style={styles.noteTitle}>{note.title}</h3>
                                                <p style={styles.noteContent}>
                                                    {expandedNotes[note.id]
                                                        ? note.content || "No additional content available."
                                                        : `${note.content?.slice(0, 20) || "No additional content"}...`}
                                                </p>
                                                <button
                                                    style={styles.expandButton}
                                                    onClick={(e) => {
                                                        e.stopPropagation();
                                                        toggleNoteExpansion(note.id);
                                                    }}
                                                >
                                                    {expandedNotes[note.id] ? "−" : "+"}
                                                </button>
                                            </div>
                                        ))
                                    ) : (
                                        <p style={styles.noNotesMessage}>No notes in this folder.</p>
                                    )}
                                </div>
                            )}
                        </div>
                    ))
                ) : (
                    <p>Loading folders...</p>
                )}
            </div>
        </div>
    );
};

const styles = {
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
    folderListContainer: {
        padding: "16px",
    },
    folderItem: {
        marginBottom: "24px",
        border: "1px solid #ccc",
        borderRadius: "8px",
        padding: "16px",
        backgroundColor: "#f9f9f9",
    },
    folderHeader: {
        display: "flex",
        alignItems: "center",
        justifyContent: "space-between",
        cursor: "pointer",
    },
    folderTitle: {
        fontSize: "1.25rem",
        color: "#333",
        margin: "0",
    },
    notesGrid: {
        display: "grid",
        gridTemplateColumns: "repeat(auto-fill, minmax(200px, 1fr))",
        gap: "16px",
        marginTop: "16px",
        padding: "16px",
        backgroundColor: "#fff",
        borderRadius: "8px",
    },
    noteCard: {
        padding: "12px",
        border: "1px solid #ddd",
        borderRadius: "8px",
        backgroundColor: "#fafafa",
        boxShadow: "0 4px 6px rgba(0, 0, 0, 0.1)",
        position: "relative",
    },
    noteTitle: {
        fontSize: "1rem",
        fontWeight: "bold",
        marginBottom: "8px",
    },
    noteContent: {
        fontSize: "0.875rem",
        color: "#555",
    },
    noNotesMessage: {
        fontStyle: "italic",
        color: "#777",
    },
};

export default FoldersListForm;