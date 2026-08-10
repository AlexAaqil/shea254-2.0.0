import './bootstrap';

// This code listens for events from Livewire components
// When Livewire dispatches an event, this code catches it and triggers the Pixel

// Helper function to normalize event data
function normalizeEventData(data) {
    const normalized = { ...data };
    
    // Ensure value is a number
    if (normalized.value !== undefined) {
        normalized.value = parseFloat(normalized.value);
    }
    
    // Ensure currency is uppercase
    if (normalized.currency) {
        normalized.currency = normalized.currency.toUpperCase();
    }
    
    return normalized;
}

document.addEventListener('DOMContentLoaded', function() {
    
    window.addEventListener('track-view-content', function(event) {
        if (typeof fbq !== 'undefined') {
            const eventData = normalizeEventData(event.detail);
            const eventId = eventData.event_id;
            delete eventData.event_id;

            fbq('track', 'ViewContent', eventData, {eventID: eventId});
            console.log('Meta Pixel: ViewContent tracked with event_id:', eventId);
        } else {
            console.warn('Meta Pixel not loaded');
        }
    });
    
    window.addEventListener('track-add-to-cart', function(event) {
        if (typeof fbq !== 'undefined') {
            const eventData = normalizeEventData(event.detail);
            const eventId = eventData.event_id;
            delete eventData.event_id;

            fbq('track', 'AddToCart', eventData, {eventID: eventId});
            console.log('Meta Pixel: AddToCart tracked with event_id:', eventId);
        } else {
            console.warn('Meta Pixel not loaded');
        }
    });
    
    window.addEventListener('track-initiate-checkout', function(event) {
        if (typeof fbq !== 'undefined') {
            const eventData = normalizeEventData(event.detail);
            fbq('track', 'InitiateCheckout', eventData);
            console.log('Meta Pixel: InitiateCheckout tracked', eventData);
        } else {
            console.warn('Meta Pixel not loaded');
        }
    });
    
    window.addEventListener('track-purchase', function(event) {
        if (typeof fbq !== 'undefined') {
            const eventData = normalizeEventData(event.detail);
            fbq('track', 'Purchase', eventData);
            console.log('Meta Pixel: Purchase tracked', eventData);
        } else {
            console.warn('Meta Pixel not loaded');
        }
    });
    
    window.addEventListener('track-meta-event', function(event) {
        if (typeof fbq !== 'undefined') {
            const { eventName, eventData } = event.detail;
            const normalizedData = normalizeEventData(eventData);
            fbq('track', eventName, normalizedData);
            console.log(`Meta Pixel: ${eventName} tracked`, normalizedData);
        } else {
            console.warn('Meta Pixel not loaded');
        }
    });
});
