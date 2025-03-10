import React from 'react';

const ContactPage = () => {
    return (
        <div style={{ padding: '20px', fontFamily: 'Arial, sans-serif', lineHeight: '1.6' }}>
            <h1>Contact</h1>
            <p>
                If you have any questions, feedback, or need support, feel free to reach out! I'd love to hear from you.
            </p>

            <h2>Contact Information</h2>
            <p>
                <strong>Email:</strong> <a href="mailto:support@example.com">support@example.example</a> <br />
                <strong>Phone:</strong> +1 (123) 456-7890 <br />
                <strong>Address:</strong> 123 Example Street, Example City, EX 12345
            </p>
            <strong>- Note that it's just an example and no real contact information.</strong>
            <h2>Contact Form</h2>
            <form
                onSubmit={(e) => {
                    e.preventDefault();
                    alert('Thank you for contacting! Note that this is just an example and no real contact has been made.');
                }}
                style={{
                    display: 'flex',
                    flexDirection: 'column',
                    maxWidth: '400px',
                    gap: '10px',
                    marginTop: '20px'
                }}
            >
                <label>
                    <strong>Name</strong>:
                    <input
                        type="text"
                        name="name"
                        placeholder="Enter your name"
                        required
                        style={{
                            width: '100%',
                            padding: '8px',
                            marginTop: '5px',
                            borderRadius: '4px',
                            border: '1px solid #ccc'
                        }}
                    />
                </label>

                <label>
                    <strong>Email</strong>:
                    <input
                        type="email"
                        name="email"
                        placeholder="Enter your email"
                        required
                        style={{
                            width: '100%',
                            padding: '8px',
                            marginTop: '5px',
                            borderRadius: '4px',
                            border: '1px solid #ccc'
                        }}
                    />
                </label>

                <label>
                    <strong>Message</strong>:
                    <textarea
                        name="message"
                        placeholder="Write your message here..."
                        rows="5"
                        required
                        style={{
                            width: '100%',
                            padding: '8px',
                            marginTop: '5px',
                            borderRadius: '4px',
                            border: '1px solid #ccc',
                            resize: 'vertical'
                        }}
                    />
                </label>

                <button
                    type="submit"
                    style={{
                        padding: '10px 15px',
                        backgroundColor: '#007bff',
                        color: '#fff',
                        border: 'none',
                        borderRadius: '4px',
                        cursor: 'pointer'
                    }}
                >
                    Send Message
                </button>
                <strong>- Note that it's just an example and no real contact form.</strong>
            </form>
        </div>
    );
}

export default ContactPage;